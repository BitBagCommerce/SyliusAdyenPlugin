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

    public function isApiKeyValid(string $environment, ?string $merchantAccount, ?string $apiKey): bool;

    public function getEnvironment(): string;

    public function getAvailablePaymentMethodsForForm(string $locale, string $countryCode, int $amount, string $currencyCode): array;

    public function submitPayment(
        int $amount,
        string $currencyCode,
        string $reference,
        string $redirectUrl,
        array $paymentData
    );
}
