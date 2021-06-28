<?php


namespace BitBag\SyliusAdyenPlugin\Traits;


use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

trait PaymentFromOrderTrait
{
    private function getMethod(PaymentInterface $payment): PaymentMethodInterface
    {
        $method = $payment->getMethod();
        if($method === null){
            throw new \InvalidArgumentException(
                sprintf('No PaymentMethod assigned to Payment #%d', $payment->getId())
            );
        }

        return $method;
    }

    private function getPayment(OrderInterface $order): PaymentInterface
    {
        $payment = $order->getLastPayment();

        if($payment === null){
            throw new \InvalidArgumentException(
                sprintf('No payment associated with Order #%d', $order->getId())
            );
        }

        return $payment;
    }

}