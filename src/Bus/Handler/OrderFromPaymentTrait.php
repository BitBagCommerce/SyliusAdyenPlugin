<?php


namespace BitBag\SyliusAdyenPlugin\Bus\Handler;


use Adyen\Service\Payment;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

trait OrderFromPaymentTrait
{
    /**
     * @param \Sylius\Component\Core\Model\PaymentInterface|PaymentInterface $payment
     */
    private function getOrderFromPayment(PaymentInterface $payment): OrderInterface
    {
        $result = $payment->getOrder();
        if($result === null){
            throw new \InvalidArgumentException(sprintf('Payment #%d has no order', $payment->getId()));
        }

        return $result;
    }
}