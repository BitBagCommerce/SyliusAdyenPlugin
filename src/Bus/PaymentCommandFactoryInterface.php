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
use BitBag\SyliusAdyenPlugin\Bus\Command\MarkPaymentAsProcessedCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentCancelledCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentFailedCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentLifecycleCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentStatusReceived;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentCommandFactoryInterface
{
    public const CAPTURE_METHOD_AUTO = 'auto';
    public const MAPPING = [
        'authorisation' => AuthorizePayment::class,
        'payment_status_received' => PaymentStatusReceived::class,
        'capture' => CapturePayment::class,
        'received' => MarkPaymentAsProcessedCommand::class,
        'refused' => PaymentFailedCommand::class,
        'rejected' => PaymentFailedCommand::class,
        'cancellation' => PaymentCancelledCommand::class,
    ];

    public function createForEvent(
        string $event,
        PaymentInterface $payment,
        ?NotificationItemData $notificationItemData = null
    ): PaymentLifecycleCommand;
}
