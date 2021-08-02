<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Serializer;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NotificationItemDenormalizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    private const PROCESSED_FLAG = '_adyen_notification_processed';

    use DenormalizerAwareTrait;

    /**
     * @psalm-suppress MixedReturnStatement
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (!isset($context[self::PROCESSED_FLAG]) && is_array($data)) {
            $data['eventCode'] = strtolower((string) $data['eventCode']);
            $data['paymentMethod'] = strtolower((string) $data['paymentMethod']);
        }

        $context[self::PROCESSED_FLAG] = true;

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return
            $type === NotificationItemData::class
            && isset($data['eventCode'], $data['paymentMethod'])

        ;
    }
}
