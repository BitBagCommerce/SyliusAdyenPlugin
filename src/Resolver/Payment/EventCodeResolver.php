<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

class EventCodeResolver
{
    public const AUTHORIZATION = 'authorisation';

    public const CAPTURE = 'capture';

    public const PAYMENT_METHOD_TYPES = [
        'visa' => self::AUTHORIZATION,
        'ideal' => self::CAPTURE
    ];

    public function resolve(array $notificationData): string
    {
        if (!isset($notificationData['eventCode'])) {
            throw new \InvalidArgumentException('eventCode is not supplied');
        }

        if ($notificationData['eventCode'] !== self::AUTHORIZATION) {
            return $notificationData['eventCode'];
        }

        return self::PAYMENT_METHOD_TYPES[strtolower($notificationData['paymentMethod'])] ?? self::AUTHORIZATION;
    }
}
