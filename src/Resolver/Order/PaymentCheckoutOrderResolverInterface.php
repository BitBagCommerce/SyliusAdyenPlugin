<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;

use Sylius\Component\Core\Model\OrderInterface;

interface PaymentCheckoutOrderResolverInterface
{
    public function resolve(): OrderInterface;
}
