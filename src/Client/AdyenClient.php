<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\AdyenException;
use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;
use Adyen\Service\Checkout;
use BitBag\SyliusAdyenPlugin\Exception\AuthenticationException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidApiKeyException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidMerchantAccountException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

final class AdyenClient implements AdyenClientInterface
{
    private $options = [
        'apiKey' => null,
        'skinCode' => null,
        'merchantAccount' => null,
        'hmacKey' => null,
        'environment' => 'test',
        'notification_method' => null,
        'notification_hmac' => null,
        'default_payment_fields' => [],
        'ws_user' => null,
        'ws_user_password' => null,
    ];

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(
        array $options,
        ClientInterface $httpClient
    ) {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'apiKey',
            /*'skinCode',*/
            'merchantAccount',
            /*'hmacKey',
            'notification_hmac',
            'ws_user',
            'ws_user_password',*/
        ]);

        $this->options = $options;
        $this->httpClient = $httpClient;
    }

    private function createClient($options): Checkout
    {
        $client = new Client(new Config([
            'httpClient'=>$this->httpClient
        ]));

        $client->setXApiKey($options['apiKey']);
        $client->setEnvironment(
            $options['environment'] == 'test' ? Environment::TEST : Environment::LIVE
        );
        $client->setTimeout(30);

        return new Checkout($client);
    }

    public function getAvailablePaymentMethodsForForm(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode
    ): array {
        $paymentMethods = $this->getAvailablePaymentMethods($locale, $countryCode, $amount, $currencyCode);
        $result = [];
        foreach ($paymentMethods['paymentMethods'] as $paymentMethod) {
            if (!empty($paymentMethod['brands'])) {
                foreach ($paymentMethod['brands'] as $brand) {
                    $result[$brand] = $paymentMethod['name'];
                }

                continue;
            }
            $result[$paymentMethod['type']] = $paymentMethod['name'];
        }

        return $result;
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

        $paymentMethods = $this->createClient($this->options)->paymentMethods($payload);

        if (!isset($paymentMethods['paymentMethods'])) {
            throw new \RuntimeException(sprintf('Adyen API failed to return any payment methods'));
        }

        return $paymentMethods;
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
            $this->createClient($options)->paymentMethods($payload);
        } catch (AdyenException $exception) {
            $this->dispatchException($exception);
        }

        return true;
    }

    public function getEnvironment(): string
    {
        return $this->options['environment'];
    }
}
