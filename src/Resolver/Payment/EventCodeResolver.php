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

class EventCodeResolver
{
    public const AUTHORIZATION = 'authorisation';

    public const CAPTURE = 'capture';

    public const PAYMENT_METHOD_TYPES = [
        'visa' => self::AUTHORIZATION,
        'ideal' => self::CAPTURE
    ];

    public function resolve(NotificationItemData $notificationData): string
    {
        if ($notificationData->eventCode !== self::AUTHORIZATION) {
            return (string) $notificationData->eventCode;
        }

        return self::PAYMENT_METHOD_TYPES[$notificationData->paymentMethod] ?? self::AUTHORIZATION;
    }
}
