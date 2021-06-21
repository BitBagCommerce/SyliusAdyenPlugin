<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentLifecycleCommand
{
    public function getPayment(): PaymentInterface;
}
