<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Repository;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class AdyenTokenRepository extends EntityRepository implements AdyenTokenRepositoryInterface
{
    public function findOneByPaymentMethodAndCustomer(
        PaymentMethodInterface $paymentMethod,
        CustomerInterface $customer,
    ): ?AdyenTokenInterface {
        $result = $this->findOneBy([
            'paymentMethod' => $paymentMethod,
            'customer' => $customer,
        ]);

        return $result instanceof AdyenTokenInterface ? $result : null;
    }
}
