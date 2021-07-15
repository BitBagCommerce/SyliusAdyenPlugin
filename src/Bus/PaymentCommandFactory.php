<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\CapturePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentLifecycleCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\EventCodeResolver;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

class PaymentCommandFactory
{
    public const MAPPING = [
        'authorisation' => AuthorizePayment::class,
        'prepare' => PreparePayment::class,
        'capture' => CapturePayment::class
    ];

    /** @var array */
    private $mapping = [];

    /** @var EventCodeResolver */
    private $eventCodeResolver;

    public function __construct(
        EventCodeResolver $eventCodeResolver,
        array $mapping = []
    ) {
        $this->mapping = array_merge_recursive(self::MAPPING, $mapping);
        $this->eventCodeResolver = $eventCodeResolver;
    }

    private function createObject(string $eventName, PaymentInterface $payment): PaymentLifecycleCommand
    {
        $class = (string) $this->mapping[$eventName];

        $result = new $class($payment);
        Assert::isInstanceOf($result, PaymentLifecycleCommand::class);

        return $result;
    }

    public function createForEvent(
        string $event,
        PaymentInterface $payment,
        array $notificationData = []
    ): PaymentLifecycleCommand {
        if (isset($notificationData['eventCode'])) {
            $event = $this->eventCodeResolver->resolve($notificationData);
        }

        $eventName = strtolower($event);

        if (!isset($this->mapping[$eventName])) {
            throw new UnmappedAdyenActionException(sprintf('Event "%s" has no handler registered', $eventName));
        }

        return $this->createObject($eventName, $payment);
    }
}
