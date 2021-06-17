<?php


namespace BitBag\SyliusAdyenPlugin\Actions;


use Sylius\Component\Core\Model\PaymentInterface;

interface AdyenAction
{
    public function __invoke(PaymentInterface $payment, array $notificationData = []);

    public function accept(PaymentInterface $payment): bool;

    public static function getHandledEventName(): string;
}