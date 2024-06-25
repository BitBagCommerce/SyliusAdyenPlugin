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

interface EventCodeResolverInterface
{
    public const CAPTURE = 'capture';
    public const AUTHORIZATION = 'authorisation';

    public const PAYMENT_METHOD_TYPES = [
        'amazonpay' => self::AUTHORIZATION,
        'applepay' => self::AUTHORIZATION,
        'klarna' => self::AUTHORIZATION,
        'paywithgoogle' => self::AUTHORIZATION,
        'twint' => self::AUTHORIZATION,
    ];

    public function resolve(NotificationItemData $notificationData): string;
}
