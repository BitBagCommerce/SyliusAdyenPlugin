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
use BitBag\SyliusAdyenPlugin\Exception\NotificationItemsEmptyException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver\NoCommandResolvedException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolverInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationToCommandResolverInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessNotificationsAction
{
    private const EXPECTED_ADYEN_RESPONSE = '[accepted]';

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var NotificationToCommandResolverInterface */
    private $notificationCommandResolver;

    /** @var NotificationResolverInterface */
    private $notificationResolver;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        DispatcherInterface $dispatcher,
        NotificationToCommandResolverInterface $notificationCommandResolver,
        NotificationResolverInterface $notificationResolver,
        LoggerInterface $logger,
    ) {
        $this->dispatcher = $dispatcher;
        $this->notificationCommandResolver = $notificationCommandResolver;
        $this->notificationResolver = $notificationResolver;
        $this->logger = $logger;
    }

    public function __invoke(string $code, Request $request): Response
    {
        try {
            $notifications = $this->notificationResolver->resolve($code, $request);
        } catch (NotificationItemsEmptyException) {
            $this->logger->error('Request payload did not contain any notification items');
            $notifications = [];
        }

        foreach ($notifications as $notificationItem) {
            if (null === $notificationItem || false === $notificationItem->success) {
                $this->logger->error(\sprintf(
                    'Payment with pspReference [%s] did not return success',
                    $notificationItem->pspReference ?? '',
                ));
            } else {
                $this->logger->debug(\sprintf(
                    'Payment with pspReference [%s] finished with event code [%s]',
                    $notificationItem->pspReference ?? '',
                    $notificationItem->eventCode ?? '',
                ));
            }

            try {
                $command = $this->notificationCommandResolver->resolve($code, $notificationItem);
                $this->dispatcher->dispatch($command);
            } catch (NoCommandResolvedException $ex) {
                $this->logger->error('Tried to dispatch an unknown command');
            }
        }

        return new Response(self::EXPECTED_ADYEN_RESPONSE);
    }
}
