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
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

trait PaymentFromOrderTrait
{
    private function getMethod(PaymentInterface $payment): PaymentMethodInterface
    {
        $method = $payment->getMethod();
        Assert::isInstanceOf($method, PaymentMethodInterface::class);

        return $method;
    }

    private function getPayment(OrderInterface $order, ?string $state = null): PaymentInterface
    {
        $payment = $order->getLastPayment($state);

        if ($payment === null) {
            throw new \InvalidArgumentException(
                sprintf('No payment associated with Order #%d', (int) $order->getId())
            );
        }

        return $payment;
    }
}
