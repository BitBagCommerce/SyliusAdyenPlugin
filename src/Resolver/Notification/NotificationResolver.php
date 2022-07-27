<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification;

use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NotificationResolver implements NotificationResolverInterface
{
    /**
     * Adyen passes booleans as strings
     */
    private const DENORMALIZATION_FORMAT = 'json';

    /** @var DenormalizerInterface */
    private $denormalizer;

    /** @var ValidatorInterface */
    private $validator;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @return NotificationItemData[]
     */
    private function denormalizeRequestData(Request $request): array
    {
        $payload = $request->request->all();

        /** @var array $notificationItems */
        $notificationItems = $payload['notificationItems'];
        $result = [];

        /** @var array $notificationItem */
        foreach ($notificationItems as $notificationItem) {
            /** @var array $notificationRequestItem */
            $notificationRequestItem = $notificationItem['NotificationRequestItem'] ?? [];

            $result[] = $this->denormalizer->denormalize(
                $notificationRequestItem,
                NotificationItemData::class,
                self::DENORMALIZATION_FORMAT
            );
        }

        return $result;
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
            if (0 < $validationResult->count()) {
                $this->logger->error(
                    'Denormalization violations: ' . \var_export($validationResult, true)
                );

                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
