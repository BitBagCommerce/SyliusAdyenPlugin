<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentRepositoryInterface
{
    public function find(int $id): ?PaymentInterface;

    public function getOneByCodeAndId(string $code, int $id): PaymentInterface;
}
