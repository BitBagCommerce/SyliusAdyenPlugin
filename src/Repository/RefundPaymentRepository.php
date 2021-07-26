<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

class RefundPaymentRepository implements RefundPaymentRepositoryInterface
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
    public function getForOrderNumberAndRefundPaymentId(
        string $orderNumber,
        int $paymentId
    ): RefundPaymentInterface {
        $qb = $this->baseRepository->createQueryBuilder('rp');
        $qb
            ->select('rp')
            ->innerJoin('rp.order', 'o')
            ->where('rp.id=:id')
            ->andWhere('o.number=:order_number')
            ->setParameters([
                'id' => $paymentId,
                'order_number' => $orderNumber
            ])
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
