<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\NotificationResolver;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use BitBag\SyliusAdyenPlugin\Repository\AdyenReferenceRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Doctrine\ORM\NoResultException;
use Webmozart\Assert\Assert;

final class RefundNotificationResolver implements CommandResolver
{
    /** @var AdyenReferenceRepositoryInterface */
    private $adyenReferenceRepository;

    public function __construct(
        AdyenReferenceRepositoryInterface $adyenReferenceRepository
    ) {
        $this->adyenReferenceRepository = $adyenReferenceRepository;
    }

    public function resolve(string $paymentCode, NotificationItemData $notificationData): object
    {
        try {
            $reference = $this->adyenReferenceRepository->getOneForRefundByCodeAndReference(
                $paymentCode,
                (string) $notificationData->pspReference
            );

            $refundPayment = $reference->getRefundPayment();
            Assert::notNull($refundPayment);

            return new RefundPayment($refundPayment);
        } catch (\InvalidArgumentException | NoResultException $ex) {
            throw new NoCommandResolvedException();
        }
    }
}
