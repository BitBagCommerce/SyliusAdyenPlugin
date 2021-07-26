<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use BitBag\SyliusAdyenPlugin\PaymentTransitions;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;

class CapturePayment implements PaymentFinalizationCommand
{
    /** @var PaymentInterface */
    private $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getPaymentTransition(): string
    {
        return PaymentTransitions::TRANSITION_CAPTURE;
    }

    public function getOrderTransition(): string
    {
        return OrderPaymentTransitions::TRANSITION_PAY;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }
}
