<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface as BasePaymentRepositoryInterface;

/**
 * @method PaymentInterface find($id)
 */
interface PaymentRepositoryInterface extends BasePaymentRepositoryInterface
{
    public function getOneByCodeAndId(string $code, int $id): PaymentInterface;
}