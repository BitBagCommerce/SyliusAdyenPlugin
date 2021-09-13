<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use BitBag\SyliusAdyenPlugin\Repository\AdyenReferenceRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Doctrine\ORM\NoResultException;
use Sylius\Component\Core\Model\PaymentInterface;

class PaymentNotificationResolver implements CommandResolver
{
    /** @var Dispatcher */
    private $dispatcher;
    private AdyenReferenceRepositoryInterface $adyenReferenceRepository;

    public function __construct(
        Dispatcher $dispatcher,
        AdyenReferenceRepositoryInterface $adyenReferenceRepository
    ) {
        $this->dispatcher = $dispatcher;
        $this->adyenReferenceRepository = $adyenReferenceRepository;
    }

    private function fetchPayment(string $paymentCode, string $reference, ?string $originalReference): PaymentInterface
    {
        try {
            $reference = $this->adyenReferenceRepository->getOneByCodeAndReference(
                $paymentCode, $originalReference ?? $reference
            );
            return $reference->getPayment();
        } catch (NoResultException $ex) {
            throw new NoCommandResolvedException();
        }
    }

    public function resolve(string $paymentCode, NotificationItemData $notificationData): object
    {
        try {
            $payment = $this->fetchPayment(
                $paymentCode, (string) $notificationData->pspReference, $notificationData->originalReference
            );

            return $this->dispatcher->getCommandFactory()->createForEvent(
                (string) $notificationData->eventCode,
                $payment,
                $notificationData
            );
        } catch (UnmappedAdyenActionException $ex) {
            throw new NoCommandResolvedException();
        }
    }
}
