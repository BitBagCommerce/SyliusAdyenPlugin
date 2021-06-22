<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class PaymentMethodRepository extends BasePaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    public function findOneForAdyenAndCode(string $code): ?PaymentMethodInterface
    {
        try {
            return $this->createQueryBuilder('o')
                ->innerJoin('o.gatewayConfig', 'gatewayConfig')
                ->where('gatewayConfig.factoryName = :factoryName')
                ->andWhere('o.code = :code')
                ->setParameter('factoryName', AdyenGatewayFactory::FACTORY_NAME)
                ->setParameter('code', $code)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    private function getQueryForChannel(ChannelInterface $channel): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig')
            ->andWhere('o.enabled = true')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('gatewayConfig.factoryName = :factoryName')
            ->setParameter('channel', $channel)
            ->setParameter('factoryName', AdyenGatewayFactory::FACTORY_NAME)
            ->addOrderBy('o.position')
        ;
    }

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
     * @return PaymentMethodInterface[]
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
