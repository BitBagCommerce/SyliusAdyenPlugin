<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\CapturePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentLifecycleCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\MarkOrderAsCompleted;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\EventCodeResolver;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

class PaymentCommandFactory
{
    public const MAPPING = [
        'authorisation' => AuthorizePayment::class,
        'prepare' => MarkOrderAsCompleted::class,
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
        if (!isset($this->mapping[$eventName])) {
            throw new UnmappedAdyenActionException(sprintf('Event "%s" has no handler registered', $eventName));
        }

        $class = (string) $this->mapping[$eventName];

        $result = new $class($payment);
        Assert::isInstanceOf($result, PaymentLifecycleCommand::class);

        return $result;
    }

    public function createForEvent(
        string $event,
        PaymentInterface $payment,
        ?NotificationItemData $notificationItemData = null
    ): PaymentLifecycleCommand {
        if ($notificationItemData !== null) {
            $event = $this->eventCodeResolver->resolve($notificationItemData);
        }

        return $this->createObject($event, $payment);
    }
}
