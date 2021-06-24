<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

trait PayableOrderPaymentTrait
{
    public function getPayablePayment(OrderInterface $order): PaymentInterface
    {
        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if ($payment === null) {
            $payment = $order->getLastPayment(PaymentInterface::STATE_CART);
        }

        if ($payment === null) {
            throw new \InvalidArgumentException(
                sprintf('Order #%d has no Payment associated', $order->getId())
            );
        }

        return $payment;
    }
}
