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
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface AdyenReferenceInterface extends ResourceInterface
{

    /**
     * @return string
     */
    public function getPspReference(): string;

    public function getRefundPayment(): ?RefundPaymentInterface;

    /**
     * @param RefundPaymentInterface|null $refund
     */
    public function setRefundPayment(?RefundPaymentInterface $refund): void;

    /**
     * @return PaymentInterface|null
     */
    public function getPayment(): ?PaymentInterface;

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param PaymentInterface|null $payment
     */
    public function setPayment(?PaymentInterface $payment): void;

    /**
     * @param string $pspReference
     */
    public function setPspReference(string $pspReference): void;
}