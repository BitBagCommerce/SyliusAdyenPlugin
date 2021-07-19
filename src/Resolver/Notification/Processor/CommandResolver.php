<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

interface CommandResolver
{
    /**
     * @throws NoCommandResolved
     *
     * @param array<string, mixed> $notificationData
     */
    public function resolve(string $paymentCode, array $notificationData): object;
}
