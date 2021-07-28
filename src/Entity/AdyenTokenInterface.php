<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface AdyenTokenInterface
{
    public function setCustomer(?CustomerInterface $customer): void;

    public function getCustomer(): ?CustomerInterface;

    public function setIdentifier(?string $identifier): void;

    public function getIdentifier(): ?string;

    public function setPaymentMethod(?PaymentMethodInterface $paymentMethod): void;

    public function getPaymentMethod(): ?PaymentMethodInterface;
}
