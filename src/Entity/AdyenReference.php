<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

class AdyenReference implements ResourceInterface, AdyenReferenceInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pspReference;

    /**
     * @var ?PaymentInterface
     */
    protected $payment;

    /**
     * @var ?RefundPaymentInterface
     */
    protected $refundPayment;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPspReference(): string
    {
        return $this->pspReference;
    }

    /**
     * @param string $pspReference
     */
    public function setPspReference(string $pspReference): void
    {
        $this->pspReference = $pspReference;
    }

    /**
     * @return PaymentInterface|null
     */
    public function getPayment(): ?PaymentInterface
    {
        return $this->payment;
    }

    /**
     * @param PaymentInterface|null $payment
     */
    public function setPayment(?PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }

    public function getRefundPayment(): ?RefundPaymentInterface
    {
        return $this->refundPayment;
    }

    /**
     * @param RefundPaymentInterface|null $refundPayment
     */
    public function setRefundPayment(?RefundPaymentInterface $refundPayment): void
    {
        $this->refundPayment = $refundPayment;
    }
}