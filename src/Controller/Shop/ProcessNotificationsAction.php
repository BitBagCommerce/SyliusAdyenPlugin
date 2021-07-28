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
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationCommandResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor\NoCommandResolvedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class ProcessNotificationsAction
{
    public const EXPECTED_ADYEN_RESPONSE = '[accepted]';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var NotificationCommandResolver */
    private $notificationCommandResolver;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        Dispatcher $dispatcher,
        NotificationCommandResolver $notificationCommandResolver
    ) {
        $this->adyenClientProvider = $adyenClientProvider;

        $this->dispatcher = $dispatcher;
        $this->notificationCommandResolver = $notificationCommandResolver;
    }

    private function validateRequest(array $arguments): void
    {
        Assert::keyExists($arguments, 'notificationItems');
        Assert::isArray($arguments['notificationItems']);
    }

    public function __invoke(string $code, Request $request): Response
    {
        $arguments = $request->request->all();
        $this->validateRequest($arguments);

        /**
         * @var array<string, array<string, mixed>> $notificationItem
         */
        foreach ($arguments['notificationItems'] as $notificationItem) {
            $notificationItem = $notificationItem['NotificationRequestItem'];

            if (isset($notificationItem['success']) && !(bool) $notificationItem['success']) {
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
