<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

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
        // todo: constants
        return 'capture';
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
