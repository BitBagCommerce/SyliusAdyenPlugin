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
            return (string) $notificationData['eventCode'];
        }

        $paymentMethodKey = strtolower((string) $notificationData['paymentMethod']);

        return self::PAYMENT_METHOD_TYPES[$paymentMethodKey] ?? self::AUTHORIZATION;
    }
}
