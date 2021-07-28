<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\CommandResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\NoCommandResolvedException;

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
            } catch (NoCommandResolvedException $ex) {
            }
        }

        throw new NoCommandResolvedException();
    }
}
