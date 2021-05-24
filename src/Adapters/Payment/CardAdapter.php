<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Adapters\Payment;

final class CardAdapter implements PaymentAdapterInterface
{
    /** @var string */
    private $setLocalizedName;

    public function setLocalizedName(string $localizedName): void
    {
        $this->setLocalizedName = $localizedName;
    }

    /**
     * @return string
     */
    public function getSetLocalizedName(): ?string
    {
        return $this->setLocalizedName;
    }

    public static function getKey(): string
    {
        return 'scheme';
    }
}
