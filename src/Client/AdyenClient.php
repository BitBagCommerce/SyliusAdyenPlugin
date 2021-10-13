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
use Payum\Core\Bridge\Spl\ArrayObject;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Webmozart\Assert\Assert;

final class AdyenClient implements AdyenClientInterface
{
    /** @var ArrayObject */
    private $options;

    /** @var Client */
    private $transport;

    /** @var ClientPayloadFactoryInterface */
    private $clientPayloadFactory;
    /** @var PaymentMethodsFilterInterface */
    private $paymentMethodsFilter;

    public function __construct(
        array $options,
        AdyenTransportFactoryInterface $adyenTransportFactory,
        ClientPayloadFactoryInterface $clientPayloadFactory,
        PaymentMethodsFilterInterface $paymentMethodsFilter
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
        $this->clientPayloadFactory = $clientPayloadFactory;
        $this->paymentMethodsFilter = $paymentMethodsFilter;
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
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array {
        $paymentMethods = (array) $this->getCheckout()->paymentMethods(
            $this->clientPayloadFactory->createForAvailablePaymentMethods($this->options, $order, $adyenToken)
        );

        Assert::keyExists($paymentMethods, 'paymentMethods');

        return $this->paymentMethodsFilter->filter($paymentMethods);
    }

    public function paymentDetails(
        array $receivedPayload,
        ?AdyenTokenInterface $adyenToken = null
    ): array {
        Assert::keyExists($receivedPayload, 'details');

        $payload = $this->clientPayloadFactory->createForPaymentDetails(
            (array) $receivedPayload['details'],
            $adyenToken
        );

        return (array) $this->getCheckout()->paymentsDetails($payload);
    }

    public function submitPayment(
        string $redirectUrl,
        array $receivedPayload,
        OrderInterface $order,
        ?AdyenTokenInterface $customerIdentifier = null
    ): array {
        if (!isset($receivedPayload['paymentMethod'])) {
            throw new \InvalidArgumentException();
        }

        $payload = $this->clientPayloadFactory->createForSubmitPayment(
            $this->options,
            $redirectUrl,
            $receivedPayload,
            $order,
            $customerIdentifier
        );

        return (array) $this->getCheckout()->payments($payload);
    }

    public function requestCapture(
        PaymentInterface $payment
    ): array {
        $params = $this->clientPayloadFactory->createForCapture($this->options, $payment);

        return (array) $this->getModification()->capture($params);
    }

    public function requestCancellation(
        PaymentInterface $payment
    ): array {
        $params = $this->clientPayloadFactory->createForCancel($this->options, $payment);

        return (array) $this->getModification()->cancel($params);
    }

    public function removeStoredToken(
        string $paymentReference,
        AdyenTokenInterface $adyenToken
    ): array {
        $params = $this->clientPayloadFactory->createForTokenRemove($this->options, $paymentReference, $adyenToken);

        return (array) $this->getRecurring()->disable($params);
    }

    public function requestRefund(
        PaymentInterface $payment,
        RefundPaymentGenerated $refund
    ): array {
        $params = $this->clientPayloadFactory->createForRefund($this->options, $payment, $refund);

        return (array) $this->getModification()->refund($params);
    }

    public function getEnvironment(): string
    {
        return (string) $this->options['environment'];
    }
}
