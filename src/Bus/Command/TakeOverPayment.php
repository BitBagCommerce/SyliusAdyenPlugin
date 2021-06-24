<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\OrderInterface;

class TakeOverPayment
{
    /** @var OrderInterface */
    private $order;

    /** @var string */
    private $paymentCode;

    public function __construct(OrderInterface $order, string $paymentCode)
    {
        $this->order = $order;
        $this->paymentCode = $paymentCode;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getPaymentCode(): string
    {
        return $this->paymentCode;
    }
}
