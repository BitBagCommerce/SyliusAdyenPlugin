<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;
use Adyen\Service\Checkout;
use Adyen\Service\Modification;
use BitBag\SyliusAdyenPlugin\Adapter\PaymentMethodsToChoiceAdapter;
use BitBag\SyliusAdyenPlugin\Exception\AuthenticationException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidApiKeyException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidMerchantAccountException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

final class AdyenClient implements AdyenClientInterface
{
    public const DEFAULT_OPTIONS = [
        'apiKey' => null,
        'merchantAccount' => null,
        'hmacKey' => null,
        'environment' => 'test',
        'authUser' => null,
        'authPassword' => null,
        'clientKey' => null
    ];

    /** @var ArrayObject */
    private $options;

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(
        array $options,
        ClientInterface $httpClient
    ) {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults(self::DEFAULT_OPTIONS);
        $options->validateNotEmpty([
            'apiKey',
            'merchantAccount',
            'hmacKey',
            'authUser',
            'authPassword',
            'clientKey'
        ]);

        $this->options = $options;
        $this->httpClient = $httpClient;
    }

    private function getCheckout($options): Checkout
    {
        return new Checkout(
            $this->createClient($options)
        );
    }

    private function getModification($options): Modification
    {
        return new Modification(
            $this->createClient($options)
        );
    }

    private function createClient($options): Client
    {
        $client = new Client(new Config([
            'httpClient'=>$this->httpClient
        ]));

        $client->setXApiKey($options['apiKey']);
        $client->setEnvironment(
            $options['environment'] == 'test' ? Environment::TEST : Environment::LIVE
        );
        $client->setTimeout(30);

        return $client;
    }

    public function getAvailablePaymentMethodsForForm(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode
    ): array {
        $paymentMethods = $this->getAvailablePaymentMethods($locale, $countryCode, $amount, $currencyCode);

        return (new PaymentMethodsToChoiceAdapter())($paymentMethods);
    }

    public function getAvailablePaymentMethods(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode
    ): array {
        $payload = [
            'amount'=>[
                'value'=>$amount,
                'currency'=> $currencyCode
            ],
            'reference' => 'payment-test',
            'merchantAccount' => $this->options['merchantAccount'],
            'countryCode' => $countryCode,
            'shopperLocale' => $locale
        ];

        $paymentMethods = $this->getCheckout($this->options)->paymentMethods($payload);

        if (!isset($paymentMethods['paymentMethods'])) {
            throw new \RuntimeException(sprintf('Adyen API failed to return any payment methods'));
        }

        return $paymentMethods;
    }

    private function getOrigin(string $url): string
    {
        $components = parse_url($url);

        $pattern = '%s://%s';
        if (!empty($components['port'])) {
            $pattern .= ':%d';
        }

        return sprintf($pattern, $components['scheme'], $components['host'], $components['port']);
    }

    public function paymentDetails(
        array $receivedPayload
    ) {
        if (empty($receivedPayload['details'])) {
            throw new \InvalidArgumentException();
        }

        return $this->getCheckout($this->options)->paymentsDetails($receivedPayload);
    }

    public function submitPayment(
        int $amount,
        string $currencyCode,
        $reference,
        string $redirectUrl,
        array $receivedPayload
    ) {
        if (empty($receivedPayload['paymentMethod'])) {
            throw new \InvalidArgumentException();
        }

        $payload = [
            'amount'=>[
                'value'=>$amount,
                'currency'=> $currencyCode
            ],
            'reference' => (string) $reference,
            'merchantAccount' => $this->options['merchantAccount'],
            'returnUrl' => $redirectUrl,
            'paymentMethod'=>$receivedPayload['paymentMethod'],
            'additionalData'=> [
                'allow3DS2' => true
            ],
            'channel' => 'web',
            'origin' => $this->getOrigin($redirectUrl)
        ];

        if (!empty($receivedPayload['browserInfo'])) {
            $payload['browserInfo'] = $receivedPayload['browserInfo'];
        }

        return $this->getCheckout($this->options)->payments($payload);
    }

    private function dispatchException(AdyenException $exception)
    {
        if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
            throw new InvalidApiKeyException();
        }

        if ($exception->getCode() === Response::HTTP_FORBIDDEN) {
            throw new InvalidMerchantAccountException();
        }

        throw $exception;
    }

    private function validateArguments(?string $merchantAccount, ?string $apiKey)
    {
        if (!$merchantAccount) {
            throw new InvalidMerchantAccountException();
        }
        if (!$apiKey) {
            throw new InvalidApiKeyException();
        }
    }

    /**
     * @throws AuthenticationException|AdyenException
     */
    public function isApiKeyValid(string $environment, ?string $merchantAccount, ?string $apiKey): bool
    {
        $this->validateArguments($merchantAccount, $apiKey);

        $payload = [
            'merchantAccount' => $merchantAccount
        ];
        $options = [
            'environment'=>$environment,
            'apiKey'=>$apiKey
        ];

        try {
            $this->getCheckout($options)->paymentMethods($payload);
        } catch (AdyenException $exception) {
            $this->dispatchException($exception);
        }

        return true;
    }

    public function requestCapture(
        string $pspReference,
        int $amount,
        string $currencyCode
    ): array {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'modificationAmount' => [
                'value'=>$amount,
                'currency'=>$currencyCode
            ],
            'originalReference' => $pspReference
        ];

        return $this->getModification($this->options)->capture($params);
    }

    public function getEnvironment(): string
    {
        return $this->options['environment'];
    }
}
