<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentRepository as BasePaymentRepositoryAlias;
use Sylius\Component\Core\Model\PaymentInterface;

class PaymentRepository extends BasePaymentRepositoryAlias implements PaymentRepositoryInterface
{
    public function findOneByCodeAndId(string $code, int $id): ?PaymentInterface
    {
        return $this->findOneBy([
            'code' => $code,
            'id' => $id
        ]);
    }
}
