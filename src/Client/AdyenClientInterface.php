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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

interface AdyenClientInterface
{
    public const TEST_ENVIRONMENT = 'test';

    public const LIVE_ENVIRONMENT = 'live';

    public const DEFAULT_OPTIONS = [
        'apiKey' => null,
        'merchantAccount' => null,
        'hmacKey' => null,
        'environment' => 'test',
        'authUser' => null,
        'authPassword' => null,
        'clientKey' => null,
    ];

    public const CREDIT_CARD_TYPE = 'scheme';

    public function getAvailablePaymentMethods(
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array;

    public function getEnvironment(): string;

    public function submitPayment(
        string $redirectUrl,
        array $receivedPayload,
        OrderInterface $order,
        ?AdyenTokenInterface $customerIdentifier = null
    ): array;

    public function paymentDetails(
        array $receivedPayload,
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array;

    public function requestRefund(
        PaymentInterface $payment,
        RefundPaymentGenerated $refund
    ): array;

    public function removeStoredToken(
        string $paymentReference,
        AdyenTokenInterface $adyenToken
    ): array;

    public function requestCancellation(PaymentInterface $payment): array;

    public function requestCapture(PaymentInterface $payment): array;
}
