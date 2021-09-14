<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

class CreateReferenceForRefund
{
    /**
     * @var PaymentInterface
     */
    private $payment;

    private RefundPaymentInterface $refundPayment;
    private string $refundReference;

    public function __construct(string $refundReference, RefundPaymentInterface $refundPayment, PaymentInterface $payment)
    {
        $details = $payment->getDetails();
        Assert::keyExists($details, 'pspReference', 'Payment pspReference is not present');

        $this->refundPayment = $refundPayment;
        $this->payment = $payment;
        $this->refundReference = $refundReference;
    }

    /**
     * @return RefundPaymentInterface
     */
    public function getRefundPayment(): RefundPaymentInterface
    {
        return $this->refundPayment;
    }

    /**
     * @return PaymentInterface
     */
    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getRefundReference(): string
    {
        return $this->refundReference;
    }




}