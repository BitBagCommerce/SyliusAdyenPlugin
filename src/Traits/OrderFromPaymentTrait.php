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

trait OrderFromPaymentTrait
{
    private function getOrderFromPayment(PaymentInterface $payment): OrderInterface
    {
        $result = $payment->getOrder();
        if (null === $result) {
            throw new \InvalidArgumentException(sprintf('Payment #%d has no order', (int) $payment->getId()));
        }

        return $result;
    }
}
