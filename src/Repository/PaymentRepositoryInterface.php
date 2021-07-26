<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentRepositoryInterface
{
    public function find(int $id): ?PaymentInterface;

    public function getOneByCodeAndId(string $code, int $id): PaymentInterface;
}
