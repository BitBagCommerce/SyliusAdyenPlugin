<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentLifecycleCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use Sylius\Component\Core\Model\PaymentInterface;

class CommandFactory
{
    public const MAPPING = [
        'authorisation' => AuthorizePayment::class,
        'prepare' => PreparePayment::class
    ];

    private $mapping = [];

    public function __construct(array $mapping = [])
    {
        $this->mapping = array_merge_recursive(self::MAPPING, $mapping);
    }

    public function createForEvent(string $event, PaymentInterface $payment): PaymentLifecycleCommand
    {
        $eventName = strtolower($event);

        if (!isset($this->mapping[$eventName])) {
            throw new \InvalidArgumentException(sprintf('Event "%s" has no handler registered', $eventName));
        }

        $class = $this->mapping[$eventName];

        return new $class($payment);
    }
}
