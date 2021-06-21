<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentLifecycleCommand;
use Sylius\Component\Core\Model\PaymentInterface;

class CommandFactory
{
    public const MAPPING = [
        'authorised' => AuthorizePayment::class
    ];

    public function createForEvent(string $event, PaymentInterface $payment): PaymentLifecycleCommand
    {
        $eventName = strtolower($event);

        if (!isset(self::MAPPING[$eventName])) {
            throw new \InvalidArgumentException(sprintf('Event "%s" has no handler registered', $eventName));
        }

        $class = self::MAPPING[$eventName];

        return new $class($payment);
    }
}
