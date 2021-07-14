<?php


namespace BitBag\SyliusAdyenPlugin\Bus\Handler;


use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RefundPaymentHandler implements MessageHandlerInterface
{
    public function __invoke(RefundPayment $command): void
    {

    }
}