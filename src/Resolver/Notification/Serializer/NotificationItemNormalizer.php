<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Serializer;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class NotificationItemNormalizer implements DenormalizerAwareInterface, DenormalizerInterface, NormalizerAwareInterface, ContextAwareNormalizerInterface
{
    private const DENORMALIZATION_PROCESSED_FLAG = '_adyen_notification_denormalization_processed';

    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    /**
     * @psalm-suppress MixedReturnStatement
     */
    public function denormalize(
        $data,
        string $type,
        string $format = null,
        array $context = [],
    ) {
        if (!isset($data[self::DENORMALIZATION_PROCESSED_FLAG]) && is_array($data)) {
            $data['eventCode'] = strtolower((string) $data['eventCode']);
            $data['success'] = 'true' === $data['success'];
            $data[self::DENORMALIZATION_PROCESSED_FLAG] = true;
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(
        mixed $data,
        ?string $type,
        string $format = null,
    ): bool {
        return
            NotificationItemData::class === $type &&
            isset($data['eventCode'], $data['paymentMethod']) &&
            !isset($data[self::DENORMALIZATION_PROCESSED_FLAG])
        ;
    }

    /**
     * @param mixed $object
     */
    private function getNormalizationMarking($object): string
    {
        Assert::isInstanceOf($object, NotificationItemData::class);

        return sprintf('%s_%s', self::DENORMALIZATION_PROCESSED_FLAG, spl_object_hash($object));
    }

    /**
     * @param NotificationItemData|mixed $object
     *
     * @return array<string, mixed>
     */
    public function normalize(
        $object,
        string $format = null,
        array $context = [],
    ) {
        if (!isset($context[$this->getNormalizationMarking($object)])) {
            $context[$this->getNormalizationMarking($object)] = true;
        }

        /**
         * @var array<string, mixed> $result
         */
        $result = $this->normalizer->normalize($object, $format, $context);
        $result['eventCode'] = strtoupper((string) $result['eventCode']);

        return $result;
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): bool {
        return
            $data instanceof NotificationItemData &&
            !isset($context[$this->getNormalizationMarking($data)])
        ;
    }
}
