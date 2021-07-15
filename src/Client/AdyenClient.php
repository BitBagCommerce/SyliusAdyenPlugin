<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Service\Checkout;
use Adyen\Service\Modification;
use BitBag\SyliusAdyenPlugin\Adapter\PaymentMethodsToChoiceAdapter;
use Payum\Core\Bridge\Spl\ArrayObject;

class AdyenClient implements AdyenClientInterface
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

    /** @var Client */
    private $transport;

    public function __construct(
        array $options,
        AdyenTransportFactory $adyenTransportFactory
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
        $this->transport = $adyenTransportFactory->create($options->getArrayCopy());
    }

    private function getCheckout(): Checkout
    {
        return new Checkout(
            $this->transport
        );
    }

    private function getModification(): Modification
    {
        return new Modification(
            $this->transport
        );
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
            'amount' => [
                'value' => $amount,
                'currency' => $currencyCode
            ],
            'reference' => 'payment-test',
            'merchantAccount' => $this->options['merchantAccount'],
            'countryCode' => $countryCode,
            'shopperLocale' => $locale
        ];

        $paymentMethods = (array) $this->getCheckout()->paymentMethods($payload);

        if (!isset($paymentMethods['paymentMethods'])) {
            throw new \RuntimeException(sprintf('Adyen API failed to return any payment methods'));
        }

        return $paymentMethods;
    }

    private function getOrigin(string $url): string
    {
        $components = parse_url($url);

        $pattern = '%s://%s';
        if (isset($components['port'])) {
            $pattern .= ':%d';
        }

        return sprintf(
            $pattern,
            $components['scheme'] ?? '',
            $components['host'] ?? '',
            $components['port'] ?? 0
        );
    }

    public function paymentDetails(
        array $receivedPayload
    ): array {
        if (!isset($receivedPayload['details'])) {
            throw new \InvalidArgumentException();
        }

        return (array) $this->getCheckout()->paymentsDetails($receivedPayload);
    }

    public function submitPayment(
        int $amount,
        string $currencyCode,
        $reference,
        string $redirectUrl,
        array $receivedPayload
    ): array {
        if (!isset($receivedPayload['paymentMethod'])) {
            throw new \InvalidArgumentException();
        }

        $payload = [
            'amount' => [
                'value' => $amount,
                'currency' => $currencyCode
            ],
            'reference' => (string) $reference,
            'merchantAccount' => $this->options['merchantAccount'],
            'returnUrl' => $redirectUrl,
            'paymentMethod' => $receivedPayload['paymentMethod'],
            'additionalData' => [
                'allow3DS2' => true
            ],
            'channel' => 'web',
            'origin' => $this->getOrigin($redirectUrl)
        ];

        if (isset($receivedPayload['browserInfo'])) {
            $payload['browserInfo'] = (array) $receivedPayload['browserInfo'];
        }

        return (array) $this->getCheckout()->payments($payload);
    }

    public function requestCapture(
        string $pspReference,
        int $amount,
        string $currencyCode
    ): array {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'modificationAmount' => [
                'value' => $amount,
                'currency' => $currencyCode
            ],
            'originalReference' => $pspReference
        ];

        return (array) $this->getModification()->capture($params);
    }

    public function requestRefund(string $pspReference, int $amount, string $currencyCode, string $reference): array
    {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'modificationAmount' => [
                'value' => $amount,
                'currency' => $currencyCode
            ],
            'reference' => $reference,
            'originalReference' => $pspReference
        ];

        return (array) $this->getModification()->refund($params);
    }

    public function getEnvironment(): string
    {
        return (string) $this->options['environment'];
    }
}
