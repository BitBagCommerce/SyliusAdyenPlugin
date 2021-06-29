<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

trait OrderFromPaymentTrait
{
    private function getOrderFromPayment(PaymentInterface $payment): OrderInterface
    {
        $result = $payment->getOrder();
        if ($result === null) {
            throw new \InvalidArgumentException(sprintf('Payment #%d has no order', (int) $payment->getId()));
        }

        return $result;
    }
}
