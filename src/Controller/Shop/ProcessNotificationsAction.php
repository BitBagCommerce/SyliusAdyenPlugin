<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationToCommandResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\NoCommandResolvedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessNotificationsAction
{
    public const EXPECTED_ADYEN_RESPONSE = '[accepted]';

    /** @var Dispatcher */
    private $dispatcher;

    /** @var NotificationToCommandResolver */
    private $notificationCommandResolver;

    /** @var NotificationResolver */
    private $notificationResolver;

    public function __construct(
        Dispatcher $dispatcher,
        NotificationToCommandResolver $notificationCommandResolver,
        NotificationResolver $notificationResolver
    ) {
        $this->dispatcher = $dispatcher;
        $this->notificationCommandResolver = $notificationCommandResolver;
        $this->notificationResolver = $notificationResolver;
    }

    public function __invoke(string $code, Request $request): Response
    {
        foreach ($this->notificationResolver->resolve($code, $request) as $notificationItem) {
            if (!$notificationItem->success) {
                continue;
            }

            try {
                $command = $this->notificationCommandResolver->resolve($code, $notificationItem);
                $this->dispatcher->dispatch($command);
            } catch (NoCommandResolvedException $ex) {
            }
        }

        return new Response(self::EXPECTED_ADYEN_RESPONSE);
    }
}
