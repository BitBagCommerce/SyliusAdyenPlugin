<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AdyenTokenFactory implements AdyenTokenFactoryInterface
{
    /** @var FactoryInterface */
    private $baseFactory;

    public function __construct(FactoryInterface $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    public function create(PaymentMethodInterface $paymentMethod, CustomerInterface $customer): AdyenTokenInterface
    {
        $result = $this->createNew();
        $result->setIdentifier(
            bin2hex(random_bytes(32)),
        );
        $result->setCustomer($customer);
        $result->setPaymentMethod($paymentMethod);

        return $result;
    }

    public function createNew(): AdyenTokenInterface
    {
        /**
         * @var AdyenTokenInterface $result
         */
        $result = $this->baseFactory->createNew();

        return $result;
    }
}
