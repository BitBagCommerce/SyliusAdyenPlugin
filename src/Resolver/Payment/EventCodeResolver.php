<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;

final class EventCodeResolver implements EventCodeResolverInterface
{
    public function resolve(NotificationItemData $notificationData): string
    {
        if (self::AUTHORIZATION !== $notificationData->eventCode) {
            return (string) $notificationData->eventCode;
        }

        // Adyen doesn't provide a "card" payment method name but specifies a brand for each, so make it generic
        if (isset($notificationData->additionalData['expiryDate'])) {
            return self::AUTHORIZATION;
        }

        return self::PAYMENT_METHOD_TYPES[$notificationData->paymentMethod] ?? self::CAPTURE;
    }
}
