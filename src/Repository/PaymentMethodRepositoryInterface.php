<?php


namespace BitBag\SyliusAdyenPlugin\Repository;


use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{
    public function findOneByChannel(ChannelInterface $channel): ?PaymentMethodInterface;

    public function findAllForAdyenAndCode(string $code): array;
}