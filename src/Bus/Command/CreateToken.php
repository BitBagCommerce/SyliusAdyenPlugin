<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\CustomerInterface;

class CreateToken
{
    /** @var CustomerInterface */
    private $customer;

    public function __construct(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }
}
