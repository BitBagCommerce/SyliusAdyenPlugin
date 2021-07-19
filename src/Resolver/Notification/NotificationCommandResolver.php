<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\CommandResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\NoCommandResolved;

class NotificationCommandResolver
{
    /** @var iterable<int, CommandResolver> */
    private $notificationResolvers;

    /**
     * @param iterable<int, CommandResolver> $notificationResolvers
     */
    public function __construct(
        iterable $notificationResolvers
    ) {
        $this->notificationResolvers = $notificationResolvers;
    }

    /**
     * @param array<string, mixed> $notificationData
     */
    public function resolve(string $paymentCode, array $notificationData): object
    {
        foreach ($this->notificationResolvers as $resolver) {
            try {
                return $resolver->resolve($paymentCode, $notificationData);
            } catch (NoCommandResolved $ex) {
            }
        }

        throw new NoCommandResolved();
    }
}
