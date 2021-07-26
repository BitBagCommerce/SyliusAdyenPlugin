<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

interface PaymentFinalizationCommand extends PaymentLifecycleCommand
{
    public function getPaymentTransition(): string;

    public function getOrderTransition(): string;
}
