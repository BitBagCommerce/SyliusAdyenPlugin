<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\OrderInterface;

class RequestCapture
{
    /** @var OrderInterface */
    private $payment;

    public function __construct(OrderInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getOrder(): OrderInterface
    {
        return $this->payment;
    }
}
