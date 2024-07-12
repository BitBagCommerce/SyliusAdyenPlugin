<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
