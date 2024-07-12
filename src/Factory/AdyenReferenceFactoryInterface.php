<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenReferenceInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface AdyenReferenceFactoryInterface extends FactoryInterface
{
    public function createForPayment(PaymentInterface $payment): AdyenReferenceInterface;

    public function createForRefund(
        string $reference,
        PaymentInterface $payment,
        RefundPaymentInterface $refundPayment,
    ): AdyenReferenceInterface;
}
