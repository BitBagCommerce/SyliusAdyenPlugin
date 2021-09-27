<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

class AdyenReference implements ResourceInterface, AdyenReferenceInterface, TimestampableInterface
{
    use TimestampableTrait;

    /** @var ?int */
    protected $id;

    /** @var ?string */
    protected $pspReference;

    /** @var ?PaymentInterface */
    protected $payment;

    /** @var ?RefundPaymentInterface */
    protected $refundPayment;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPspReference(): ?string
    {
        return $this->pspReference;
    }

    public function setPspReference(string $pspReference): void
    {
        $this->pspReference = $pspReference;
    }

    public function getPayment(): ?PaymentInterface
    {
        return $this->payment;
    }

    public function setPayment(?PaymentInterface $payment): void
    {
        $this->payment = $payment;
    }

    public function getRefundPayment(): ?RefundPaymentInterface
    {
        return $this->refundPayment;
    }

    public function setRefundPayment(?RefundPaymentInterface $refundPayment): void
    {
        $this->refundPayment = $refundPayment;
    }
}
