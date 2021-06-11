<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use Sylius\Component\Core\Model\OrderInterface;

interface CommandInterface
{
    public static function createForOrder(OrderInterface $order, ?array $response = null);

    public function getOrder(): OrderInterface;
}
