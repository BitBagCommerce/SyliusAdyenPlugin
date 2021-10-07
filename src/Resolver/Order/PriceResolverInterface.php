<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;


use Sylius\Component\Core\Model\OrderItemUnitInterface;

interface PriceResolverInterface
{
    public function getNetPrice(OrderItemUnitInterface $orderItemUnit): int;

    public function getPrice(OrderItemUnitInterface $orderItemUnit): int;
}