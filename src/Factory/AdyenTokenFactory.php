<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class AdyenTokenFactory implements AdyenTokenFactoryInterface
{
    /** @var FactoryInterface */
    private $baseFactory;

    public function __construct(FactoryInterface $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    public function create(PaymentMethodInterface $paymentMethod, CustomerInterface $customer): AdyenTokenInterface
    {
        /**
         * @var AdyenTokenInterface $result
         */
        $result = $this->baseFactory->createNew();
        $result->setIdentifier(
            bin2hex(random_bytes(32))
        );
        $result->setCustomer($customer);
        $result->setPaymentMethod($paymentMethod);

        return $result;
    }
}
