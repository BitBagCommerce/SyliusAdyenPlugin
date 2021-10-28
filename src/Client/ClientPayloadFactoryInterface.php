<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

interface ClientPayloadFactoryInterface
{
    public const NO_COUNTRY_AVAILABLE_PLACEHOLDER = 'ZZ';

    public function createForAvailablePaymentMethods(ArrayObject $options, OrderInterface $order, ?AdyenTokenInterface $adyenToken = null): array;

    public function createForPaymentDetails(array $receivedPayload, ?AdyenTokenInterface $adyenToken = null): array;

    public function createForSubmitPayment(ArrayObject $options, string $url, array $receivedPayload, OrderInterface $order, ?AdyenTokenInterface $adyenToken = null): array;

    public function createForCapture(ArrayObject $options, PaymentInterface $payment): array;

    public function createForCancel(ArrayObject $options, PaymentInterface $payment): array;

    public function createForTokenRemove(ArrayObject $options, string $paymentReference, AdyenTokenInterface $adyenToken): array;

    public function createForRefund(
        ArrayObject $options,
        PaymentInterface $payment,
        RefundPaymentGenerated $refund
    ): array;
}
