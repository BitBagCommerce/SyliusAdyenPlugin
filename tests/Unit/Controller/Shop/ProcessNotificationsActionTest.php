<?php
declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Controller\Shop\ProcessNotificationsAction;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolverInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationToCommandResolverInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Mock\RequestMother;

final class ProcessNotificationsActionTest extends TestCase
{
    public function testUndefinedNotificationItemsRequestIsHandledProperly(): void
    {
        $action = new ProcessNotificationsAction(
            $this->createMock(DispatcherInterface::class),
            $this->createMock(NotificationToCommandResolverInterface::class),
            $this->createMock(NotificationResolverInterface::class),
            new  NullLogger()
        );

        $response = $action('dummy-code', RequestMother::createDummy());

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('[accepted]', $response->getContent());
    }
}
