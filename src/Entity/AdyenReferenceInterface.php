<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface AdyenReferenceInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getPspReference(): ?string;

    public function getRefundPayment(): ?RefundPaymentInterface;

    public function setRefundPayment(?RefundPaymentInterface $refundPayment): void;

    public function getPayment(): ?PaymentInterface;

    /**
     * @return int
     */
    public function getId(): ?int;

    public function setPayment(?PaymentInterface $payment): void;

    public function setPspReference(string $pspReference): void;
}
