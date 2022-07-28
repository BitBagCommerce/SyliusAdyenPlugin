<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

final class CreateReferenceForRefund
{
    /** @var PaymentInterface */
    private $payment;

    /** @var RefundPaymentInterface */
    private $refundPayment;

    /** @var string */
    private $refundReference;

    public function __construct(
        string $refundReference,
        RefundPaymentInterface $refundPayment,
        PaymentInterface $payment
    ) {
        $details = $payment->getDetails();
        Assert::keyExists($details, 'pspReference', 'Payment pspReference is not present');

        $this->refundPayment = $refundPayment;
        $this->payment = $payment;
        $this->refundReference = $refundReference;
    }

    public function getRefundPayment(): RefundPaymentInterface
    {
        return $this->refundPayment;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    public function getRefundReference(): string
    {
        return $this->refundReference;
    }
}
