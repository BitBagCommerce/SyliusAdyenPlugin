<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class CreateToken
{
    /** @var CustomerInterface */
    private $customer;

    /** @var PaymentMethodInterface */
    private $paymentMethod;

    public function __construct(PaymentMethodInterface $paymentMethod, CustomerInterface $customer)
    {
        $this->customer = $customer;
        $this->paymentMethod = $paymentMethod;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
