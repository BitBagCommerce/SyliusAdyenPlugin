<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Payment\PaymentTransitions;

class AuthorizePayment implements PaymentFinalizationCommand
{
    /** @var PaymentInterface */
    private $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }

    public function getPaymentTransition(): string
    {
        return PaymentTransitions::TRANSITION_AUTHORIZE;
    }

    public function getOrderTransition(): string
    {
        return OrderPaymentTransitions::TRANSITION_AUTHORIZE;
    }
}
