<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentRepository as BasePaymentRepository;
use Sylius\Component\Core\Model\PaymentInterface;

class PaymentRepository extends BasePaymentRepository implements PaymentRepositoryInterface
{
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function getOneByCodeAndId(string $code, int $id): PaymentInterface
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select('p')
            ->innerJoin('p.method', 'pm')
            ->where('pm.code=:code')
            ->andWhere('p.id=:id')
            ->setParameters([
                'code' => $code,
                'id' => $id
            ])
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
