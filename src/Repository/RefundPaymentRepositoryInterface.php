<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentRepositoryInterface extends RepositoryInterface
{
    public function getForOrderNumberAndRefundPaymentId(string $orderNumber, int $paymentId): RefundPaymentInterface;
}
