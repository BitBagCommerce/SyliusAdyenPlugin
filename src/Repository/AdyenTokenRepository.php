<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

/**
 * @extends EntityRepository<AdyenTokenInterface>
 */
class AdyenTokenRepository extends EntityRepository implements AdyenTokenRepositoryInterface
{
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findOneByPaymentMethodAndCustomer(
        PaymentMethodInterface $paymentMethod,
        CustomerInterface $customer
    ): ?AdyenTokenInterface {
        return $this->findOneBy([
            'paymentMethod' => $paymentMethod,
            'customer' => $customer
        ]);
    }
}
