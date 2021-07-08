<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;

class AdyenTokenRepository extends EntityRepository implements AdyenTokenRepositoryInterface
{
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findOneByCustomer(CustomerInterface $customer): ?AdyenTokenInterface
    {
        return $this->findOneBy([
            'customer' => $customer
        ]);
    }
}
