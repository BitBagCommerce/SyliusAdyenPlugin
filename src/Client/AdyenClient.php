<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Service\Checkout;
use Adyen\Service\Modification;
use Adyen\Service\Recurring;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Exception\PaymentMethodsResponseMissing;
use BitBag\SyliusAdyenPlugin\Resolver\Version\VersionResolverInterface;
use Payum\Core\Bridge\Spl\ArrayObject;

final class AdyenClient implements AdyenClientInterface
{
    /** @var ArrayObject */
    private $options;

    /** @var Client */
    private $transport;

    /** @var VersionResolverInterface */
    private $versionResolver;

    public function __construct(
        array $options,
        AdyenTransportFactoryInterface $adyenTransportFactory,
        VersionResolverInterface $versionResolver
    ) {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults(self::DEFAULT_OPTIONS);
        $options->validateNotEmpty([
            'apiKey',
            'merchantAccount',
            'hmacKey',
            'authUser',
            'authPassword',
            'clientKey',
        ]);

        $this->options = $options;
        $this->transport = $adyenTransportFactory->create($options->getArrayCopy());
        $this->versionResolver = $versionResolver;
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

    private function getRecurring(): Recurring
    {
        return new Recurring(
            $this->transport
        );
    }

    public function getAvailablePaymentMethods(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode,
        ?AdyenTokenInterface $adyenToken = null
    ): array {
        $payload = [
            'amount' => [
                'value' => $amount,
                'currency' => $currencyCode,
            ],
            'merchantAccount' => $this->options['merchantAccount'],
            'countryCode' => $countryCode,
            'shopperLocale' => $locale,
        ];

        $payload = $this->enableOneOffPayment($payload, $adyenToken);
        $payload = $this->versionResolver->appendVersionConstraints($payload);

        $paymentMethods = (array) $this->getCheckout()->paymentMethods($payload);

        if (!isset($paymentMethods['paymentMethods'])) {
            throw new PaymentMethodsResponseMissing();
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
            $components[self::CREDIT_CARD_TYPE] ?? '',
            $components['host'] ?? '',
            $components['port'] ?? 0
        );
    }

    public function paymentDetails(
        array $receivedPayload,
        ?AdyenTokenInterface $adyenToken = null
    ): array {
        if (!isset($receivedPayload['details'])) {
            throw new \InvalidArgumentException();
        }

        $receivedPayload = $this->enableOneOffPayment($receivedPayload, $adyenToken);
        $receivedPayload = $this->versionResolver->appendVersionConstraints($receivedPayload);

        return (array) $this->getCheckout()->paymentsDetails($receivedPayload);
    }

    private function isTokenizationSupported(array $payload, ?AdyenTokenInterface $customerIdentifier): bool
    {
        if ($customerIdentifier === null) {
            return false;
        }

        if (isset($payload['paymentMethod']['type']) && $payload['paymentMethod']['type'] !== self::CREDIT_CARD_TYPE) {
            return false;
        }

        return true;
    }

    private function enableOneOffPayment(
        array $payload,
        ?AdyenTokenInterface $customerIdentifier,
        bool $store = false
    ): array {
        if (!$this->isTokenizationSupported($payload, $customerIdentifier)) {
            return $payload;
        }

        if ($store) {
            $payload['storePaymentMethod'] = true;
        }

        $payload['recurringProcessingModel'] = 'CardOnFile';
        $payload['shopperInteraction'] = 'Ecommerce';
        $payload['shopperReference'] = ($customerIdentifier === null ? '' : $customerIdentifier->getIdentifier());

        return $payload;
    }

    public function submitPayment(
        int $amount,
        string $currencyCode,
        $reference,
        string $redirectUrl,
        array $receivedPayload,
        ?AdyenTokenInterface $customerIdentifier = null
    ): array {
        if (!isset($receivedPayload['paymentMethod'])) {
            throw new \InvalidArgumentException();
        }

        $payload = [
            'amount' => [
                'value' => $amount,
                'currency' => $currencyCode,
            ],
            'reference' => (string) $reference,
            'merchantAccount' => $this->options['merchantAccount'],
            'returnUrl' => $redirectUrl,
            'paymentMethod' => $receivedPayload['paymentMethod'],
            'additionalData' => [
                'allow3DS2' => true,
            ],
            'channel' => 'web',
            'origin' => $this->getOrigin($redirectUrl),
        ];

        if (isset($receivedPayload['browserInfo'])) {
            $payload['browserInfo'] = (array) $receivedPayload['browserInfo'];
        }

        $payload = $this->enableOneOffPayment(
            $payload,
            $customerIdentifier,
            (bool) ($receivedPayload['storePaymentMethod'] ?? false)
        );
        $payload = $this->versionResolver->appendVersionConstraints($payload);

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
                'currency' => $currencyCode,
            ],
            'originalReference' => $pspReference,
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return (array) $this->getModification()->capture($params);
    }

    public function requestCancellation(
        string $pspReference
    ): array {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'originalReference' => $pspReference,
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return (array) $this->getModification()->cancel($params);
    }

    public function removeStoredToken(
        string $paymentReference,
        string $shopperReference
    ): array {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'recurringDetailReference' => $paymentReference,
            'shopperReference' => $shopperReference,
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return (array) $this->getRecurring()->disable($params);
    }

    public function requestRefund(string $pspReference, int $amount, string $currencyCode, string $reference): array
    {
        $params = [
            'merchantAccount' => $this->options['merchantAccount'],
            'modificationAmount' => [
                'value' => $amount,
                'currency' => $currencyCode,
            ],
            'reference' => $reference,
            'originalReference' => $pspReference,
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return (array) $this->getModification()->refund($params);
    }

    public function getEnvironment(): string
    {
        return (string) $this->options['environment'];
    }
}
