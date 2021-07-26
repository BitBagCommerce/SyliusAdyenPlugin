<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentRepositoryInterface
{
    public function getForOrderNumberAndRefundPaymentId(string $orderNumber, int $paymentId): RefundPaymentInterface;
}
