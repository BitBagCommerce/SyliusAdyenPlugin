<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Exception\NotificationItemsEmptyException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Mock\RequestMother;

final class NotificationResolverTest extends TestCase
{
    public function testUndefinedNotificationItemsRequestIsHandledProperly(): void
    {
        $resolver = new NotificationResolver(
            $this->createMock(DenormalizerInterface::class),
            $this->createMock(ValidatorInterface::class),
            new NullLogger(),
        );

        $this->expectException(NotificationItemsEmptyException::class);

        $resolver->resolve('dummy-payment-code', RequestMother::createDummy());
    }
}
