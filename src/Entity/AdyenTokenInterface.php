<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface AdyenTokenInterface
{
    public function setCustomer(?CustomerInterface $customer): void;

    public function getCustomer(): ?CustomerInterface;

    public function setIdentifier(?string $identifier): void;

    public function getIdentifier(): ?string;

    public function setPaymentMethod(?PaymentMethodInterface $paymentMethod): void;

    public function getPaymentMethod(): ?PaymentMethodInterface;
}
