<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PaymentMethodRepository implements PaymentMethodRepositoryInterface
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
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function find(int $id): ?PaymentMethodInterface
    {
        return $this->baseRepository->find($id);
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function getOneForAdyenAndCode(string $code): PaymentMethodInterface
    {
        return $this->baseRepository->createQueryBuilder('o')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->where('gatewayConfig.factoryName = :factoryName')
            ->andWhere('o.code = :code')
            ->setParameter('factoryName', AdyenClientProviderInterface::FACTORY_NAME)
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findOneForAdyenAndCode(string $code): ?PaymentMethodInterface
    {
        try {
            return $this->getOneForAdyenAndCode($code);
        } catch (NoResultException $ex) {
            return null;
        }
    }

    /**
     * @psalm-suppress QueryBuilderSetParameter
     */
    private function getQueryForChannel(ChannelInterface $channel): QueryBuilder
    {
        return $this->baseRepository->createQueryBuilder('o')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->andWhere('o.enabled = true')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('gatewayConfig.factoryName = :factoryName')
            ->setParameter('channel', $channel)
            ->setParameter('factoryName', AdyenClientProviderInterface::FACTORY_NAME)
            ->addOrderBy('o.position')
        ;
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findOneByChannel(ChannelInterface $channel): ?PaymentMethodInterface
    {
        return $this
            ->getQueryForChannel($channel)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array<int, PaymentMethodInterface>
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function findAllByChannel(ChannelInterface $channel): array
    {
        return $this
            ->getQueryForChannel($channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
