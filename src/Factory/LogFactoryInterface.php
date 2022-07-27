<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\LogInterface;

interface LogFactoryInterface
{
    public function create(
        string $message,
        int $level,
        int $errorCode
    ): LogInterface;
}
