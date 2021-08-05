<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;
use Doctrine\ORM\NoResultException;

class RefundNotificationResolver implements CommandResolver
{
    /** @var RefundReferenceResolver */
    private $referenceResolver;

    public function __construct(
        RefundReferenceResolver $referenceResolver
    ) {
        $this->referenceResolver = $referenceResolver;
    }

    public function resolve(string $paymentCode, NotificationItemData $notificationData): object
    {
        try {
            $refundPayment = $this->referenceResolver->resolve((string) $notificationData->merchantReference);

            return new RefundPayment($refundPayment);
        } catch (\InvalidArgumentException | NoResultException $ex) {
            throw new NoCommandResolvedException();
        }
    }
}
