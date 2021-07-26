<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin;

use Sylius\Component\Payment\PaymentTransitions as BasePaymentTransitions;

interface PaymentTransitions extends BasePaymentTransitions
{
    public const TRANSITION_CAPTURE = 'capture';
}
