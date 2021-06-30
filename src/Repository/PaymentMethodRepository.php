<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentMethodRepository extends BasePaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function getOneForAdyenAndCode(string $code): PaymentMethodInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->where('gatewayConfig.factoryName = :factoryName')
            ->andWhere('o.code = :code')
            ->setParameter('factoryName', AdyenClientProvider::FACTORY_NAME)
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
        return $this->createQueryBuilder('o')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->andWhere('o.enabled = true')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('gatewayConfig.factoryName = :factoryName')
            ->setParameter('channel', $channel)
            ->setParameter('factoryName', AdyenClientProvider::FACTORY_NAME)
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
