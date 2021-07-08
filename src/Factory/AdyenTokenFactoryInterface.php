<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface AdyenTokenFactoryInterface
{
    public function create(CustomerInterface $customer): AdyenTokenInterface;
}
