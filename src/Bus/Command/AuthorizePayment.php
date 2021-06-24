<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;

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

    public function getTargetPaymentState(): string
    {
        return PaymentInterface::STATE_AUTHORIZED;
    }

    public function getOrderTransition(): string
    {
        return OrderPaymentTransitions::TRANSITION_AUTHORIZE;
    }
}
