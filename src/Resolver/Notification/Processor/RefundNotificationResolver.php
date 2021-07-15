<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;

class RefundNotificationResolver implements CommandResolver
{
    /** @var RefundReferenceResolver */
    private $referenceResolver;

    public function __construct(
        RefundReferenceResolver $referenceResolver
    ) {
        $this->referenceResolver = $referenceResolver;
    }

    public function resolve(string $paymentCode, array $notificationData): object
    {
        try {
            $refundPayment = $this->referenceResolver->resolve($notificationData['merchantReference']);

            return new RefundPayment($refundPayment);
        } catch (\InvalidArgumentException $ex) {
            throw new NoCommandResolved();
        }
    }
}
