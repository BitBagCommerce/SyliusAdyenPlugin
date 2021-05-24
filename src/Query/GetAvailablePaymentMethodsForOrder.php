<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Query;

use Sylius\Component\Core\Model\OrderInterface;

class GetAvailablePaymentMethodsForOrder
{
    private $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }
}
