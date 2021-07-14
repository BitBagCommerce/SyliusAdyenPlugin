<?php


namespace BitBag\SyliusAdyenPlugin\Bus\Handler;


use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RefundPaymentGeneratedHandler implements MessageHandlerInterface
{
    public function __construct()
    {
    }


    public function __invoke(RefundPaymentGenerated $paymentGenerated): void
    {
        // TODO: Implement __invoke() method.
    }

}