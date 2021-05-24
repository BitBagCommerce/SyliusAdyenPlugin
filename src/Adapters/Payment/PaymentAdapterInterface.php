<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Adapters\Payment;

interface PaymentAdapterInterface
{
    public static function getKey(): string;

    public function getSetLocalizedName(): ?string;

    public function setLocalizedName(string $localizedName): void;
}
