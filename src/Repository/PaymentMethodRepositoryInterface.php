<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{
    public function findOneByChannel(ChannelInterface $channel): ?PaymentMethodInterface;

    /**
     * @return PaymentMethodInterface
     */
    public function findOneForAdyenAndCode(string $code): ?PaymentMethodInterface;

    /**
     * @return PaymentMethodInterface[]
     */
    public function findAllByChannel(ChannelInterface $channel): array;
}
