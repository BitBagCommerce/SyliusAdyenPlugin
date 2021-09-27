<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Exception\NotificationItemsEmptyException;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItem;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NotificationResolver implements NotificationResolverInterface
{
    /**
     * Adyen passes booleans as strings
     */
    private const DENORMALIZATION_FORMAT = 'xml';

    /** @var DenormalizerInterface */
    private $denormalizer;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator
    ) {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    /**
     * @return NotificationItem[]
     */
    private function denormalizeRequestData(Request $request): array
    {
        $payload = $request->request->all();

        $objects = $this->denormalizer->denormalize(
            $payload,
            NotificationRequest::class,
            self::DENORMALIZATION_FORMAT
        );

        if (!is_array($objects->notificationItems)) {
            throw new NotificationItemsEmptyException();
        }

        return $objects->notificationItems;
    }

    /**
     * @return NotificationItemData[]
     */
    public function resolve(string $paymentCode, Request $request): array
    {
        $result = [];
        foreach ($this->denormalizeRequestData($request) as $item) {
            $item->paymentCode = $paymentCode;

            $validationResult = $this->validator->validate($item);
            if ($validationResult->count() > 0) {
                continue;
            }

            $result[] = $item->notificationRequestItem;
        }

        return $result;
    }
}
