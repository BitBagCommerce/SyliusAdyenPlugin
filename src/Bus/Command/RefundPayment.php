<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

class RefundPayment
{
    /** @var RefundPaymentInterface */
    private $refundPayment;

    public function __construct(RefundPaymentInterface $refundPayment)
    {
        $this->refundPayment = $refundPayment;
    }

    public function getRefundPayment(): RefundPaymentInterface
    {
        return $this->refundPayment;
    }
}
