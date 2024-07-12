<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Mock;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class NullMessageBus implements MessageBusInterface
{
    public function dispatch($message, array $stamps = []): Envelope
    {
        return Envelope::wrap(new \stdClass(), [new HandledStamp(true, static::class)]);
    }
}
