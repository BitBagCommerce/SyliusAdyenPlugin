<?php

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
