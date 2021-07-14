<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

interface AdyenClientInterface
{
    public const TEST_ENVIRONMENT = 'test';

    public const LIVE_ENVIRONMENT = 'live';

    public function getAvailablePaymentMethods(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode
    ): array;

    public function getEnvironment(): string;

    public function getAvailablePaymentMethodsForForm(string $locale, string $countryCode, int $amount, string $currencyCode): array;

    /**
     * @param mixed $reference
     */
    public function submitPayment(
        int $amount,
        string $currencyCode,
        $reference,
        string $redirectUrl,
        array $receivedPayload
    ): array;

    public function paymentDetails(array $receivedPayload): array;

    public function requestRefund(string $pspReference, int $amount, string $currencyCode, string $reference): array;
}
