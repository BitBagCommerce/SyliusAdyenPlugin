<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentRepository implements RefundPaymentRepositoryInterface
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
                'order_number' => $orderNumber,
            ])
        ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function find(int $id): ?RefundPaymentInterface
    {
        return $this->baseRepository->find($id);
    }
}
