<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\PaymentInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    /** @var EntityRepository */
    private $baseRepository;

    public function __construct(EntityRepository $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function find(int $id): ?PaymentInterface
    {
        return $this->baseRepository->find($id);
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function getOneByCodeAndId(string $code, int $id): PaymentInterface
    {
        $qb = $this->baseRepository->createQueryBuilder('p');
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
