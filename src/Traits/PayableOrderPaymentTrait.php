<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

trait PayableOrderPaymentTrait
{
    public function getPayablePayment(OrderInterface $order): PaymentInterface
    {
        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $payment) {
            $payment = $order->getLastPayment(PaymentInterface::STATE_CART);
        }

        if (null === $payment) {
            throw new \InvalidArgumentException(
                sprintf('Order #%d has no Payment associated', (int) $order->getId())
            );
        }

        return $payment;
    }
}
