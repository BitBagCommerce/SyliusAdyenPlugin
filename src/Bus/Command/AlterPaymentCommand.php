<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\OrderInterface;

interface AlterPaymentCommand
{
    public function getOrder(): OrderInterface;
}
