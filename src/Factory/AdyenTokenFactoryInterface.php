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

interface AdyenTokenFactoryInterface extends FactoryInterface
{
    public function create(PaymentMethodInterface $paymentMethod, CustomerInterface $customer): AdyenTokenInterface;
}
