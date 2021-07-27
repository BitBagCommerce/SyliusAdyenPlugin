<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;

interface AdyenClientInterface
{
    public const TEST_ENVIRONMENT = 'test';

    public const LIVE_ENVIRONMENT = 'live';

    public function getAvailablePaymentMethods(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode,
        ?AdyenTokenInterface $adyenToken = null
    ): array;

    public function getEnvironment(): string;

    /**
     * @param mixed $reference
     */
    public function submitPayment(
        int $amount,
        string $currencyCode,
        $reference,
        string $redirectUrl,
        array $receivedPayload,
        ?AdyenTokenInterface $customerIdentifier = null
    ): array;

    public function paymentDetails(array $receivedPayload): array;

    public function requestRefund(string $pspReference, int $amount, string $currencyCode, string $reference): array;

    public function removeStoredToken(string $paymentReference, string $shopperReference): array;

    public function requestCancellation(string $pspReference): array;

    public function requestCapture(string $pspReference, int $amount, string $currencyCode): array;
}
