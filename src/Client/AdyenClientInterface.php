<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

interface AdyenClientInterface
{
    public const TEST_ENVIRONMENT = 'test';
    public const LIVE_ENVIRONMENT = 'live';

    public function getAvailablePaymentMethods(
        string $locale, string $countryCode, int $amount, string $currencyCode
    ): array;
}
