<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver\NoCommandResolvedException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationToCommandResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessNotificationsAction
{
    public const EXPECTED_ADYEN_RESPONSE = '[accepted]';

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var NotificationToCommandResolver */
    private $notificationCommandResolver;

    /** @var NotificationResolver */
    private $notificationResolver;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        DispatcherInterface $dispatcher,
        NotificationToCommandResolver $notificationCommandResolver,
        NotificationResolver $notificationResolver,
        LoggerInterface $logger
    ) {
        $this->dispatcher = $dispatcher;
        $this->notificationCommandResolver = $notificationCommandResolver;
        $this->notificationResolver = $notificationResolver;
        $this->logger = $logger;
    }

    public function __invoke(string $code, Request $request): Response
    {
        foreach ($this->notificationResolver->resolve($code, $request) as $notificationItem) {
            if (!$notificationItem->success) {
                $this->logger->info(\sprintf(
                    'Payment with pspReference [%s] did not return success',
                    $notificationItem->pspReference ?? ''
                ));
            }

            try {
                $command = $this->notificationCommandResolver->resolve($code, $notificationItem);
                $this->dispatcher->dispatch($command);
            } catch (NoCommandResolvedException $ex) {
                $this->logger->error($ex->getMessage());
            }
        }

        return new Response(self::EXPECTED_ADYEN_RESPONSE);
    }
}
