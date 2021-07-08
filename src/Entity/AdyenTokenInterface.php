<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\CustomerInterface;

interface AdyenTokenInterface
{
    public function setCustomer(?CustomerInterface $customer): void;

    public function getCustomer(): ?CustomerInterface;

    public function setIdentifier(?string $identifier): void;

    public function getIdentifier(): ?string;
}
