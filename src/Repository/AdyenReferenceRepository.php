<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Entity\AdyenReferenceInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class AdyenReferenceRepository extends EntityRepository implements AdyenReferenceRepositoryInterface
{
    private function getQueryBuilderForCodeAndReference(string $code, string $pspReference): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->innerJoin('r.payment', 'p')
            ->innerJoin('p.method', 'pm')
            ->where('r.pspReference = :reference AND pm.code = :code')
            ->setParameters([
                'reference' => $pspReference,
                'code' => $code,
            ])
        ;

        return $qb;
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function getOneByCodeAndReference(string $code, string $pspReference): AdyenReferenceInterface
    {
        return $this->getQueryBuilderForCodeAndReference($code, $pspReference)->getQuery()->getSingleResult();
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     *
     * @throws NoResultException
     */
    public function getOneForRefundByCodeAndReference(string $code, string $pspReference): AdyenReferenceInterface
    {
        $qb = $this->getQueryBuilderForCodeAndReference($code, $pspReference);
        $qb->andWhere('r.refundPayment IS NOT NULL');

        return $qb->getQuery()->getSingleResult();
    }
}
