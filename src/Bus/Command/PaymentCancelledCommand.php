<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use BitBag\SyliusAdyenPlugin\PaymentTransitions;
use Sylius\Component\Core\Model\PaymentInterface;

final class PaymentCancelledCommand implements PaymentFinalizationCommand
{
    /** @var PaymentInterface */
    private $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getPaymentTransition(): string
    {
        return PaymentTransitions::TRANSITION_CANCEL;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }
}
