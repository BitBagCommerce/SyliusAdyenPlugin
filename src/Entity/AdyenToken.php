<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class AdyenToken implements ResourceInterface, AdyenTokenInterface
{
    /** @var ?int */
    protected $id;

    /** @var ?CustomerInterface */
    protected $customer;

    /** @var ?string */
    protected $identifier;

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
