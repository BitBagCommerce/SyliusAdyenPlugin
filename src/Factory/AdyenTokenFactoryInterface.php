<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface AdyenTokenFactoryInterface
{
    public function create(PaymentMethodInterface $paymentMethod, CustomerInterface $customer): AdyenTokenInterface;
}
