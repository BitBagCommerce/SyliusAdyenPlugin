<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver\CommandResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver\NoCommandResolvedException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;

final class NotificationToCommandResolver implements NotificationToCommandResolverInterface
{
    /** @var iterable<int, CommandResolver> */
    private $commandResolvers;

    /**
     * @param iterable<int, CommandResolver> $commandResolvers
     */
    public function __construct(
        iterable $commandResolvers,
    ) {
        $this->commandResolvers = $commandResolvers;
    }

    public function resolve(string $paymentCode, NotificationItemData $notificationData): object
    {
        foreach ($this->commandResolvers as $resolver) {
            try {
                return $resolver->resolve($paymentCode, $notificationData);
            } catch (NoCommandResolvedException $ex) {
            }
        }

        throw new NoCommandResolvedException();
    }
}
