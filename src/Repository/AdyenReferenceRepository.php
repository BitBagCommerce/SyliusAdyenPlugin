<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Entity\AdyenReferenceInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class AdyenReferenceRepository extends EntityRepository implements AdyenReferenceRepositoryInterface
{
    public function getOneByCodeAndReference(string $code, string $pspReference): AdyenReferenceInterface
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->innerJoin('r.payment', 'p')
            ->innerJoin('p.method', 'pm')
            ->where('r.pspReference = :reference AND pm.code = :code')
            ->setParameters([
                'reference' => $pspReference,
                'code' => $code
            ])
        ;

        return $qb->getQuery()->getSingleResult();
    }
}