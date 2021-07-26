<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

interface CommandResolver
{
    /**
     * @throws NoCommandResolvedException
     *
     * @param array<string, mixed> $notificationData
     */
    public function resolve(string $paymentCode, array $notificationData): object;
}
