<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Exception;

use Sylius\Component\Core\Model\OrderInterface;
use Throwable;

class OrderWithoutCustomerException extends \InvalidArgumentException
{
    public function __construct(OrderInterface $order, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('An order %d has no customer associated', (int) $order->getId()),
            0,
            $previous
        );
    }
}
