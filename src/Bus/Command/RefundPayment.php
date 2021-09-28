<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPayment
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
