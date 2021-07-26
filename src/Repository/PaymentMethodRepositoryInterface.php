<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface PaymentMethodRepositoryInterface
{
    public function find(int $id): ?PaymentMethodInterface;

    public function findOneByChannel(ChannelInterface $channel): ?PaymentMethodInterface;

    public function findOneForAdyenAndCode(string $code): ?PaymentMethodInterface;

    /**
     * @return PaymentMethodInterface[]
     */
    public function findAllByChannel(ChannelInterface $channel): array;

    public function getOneForAdyenAndCode(string $code): PaymentMethodInterface;
}
