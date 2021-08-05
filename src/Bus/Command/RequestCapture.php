<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\OrderInterface;

class RequestCapture implements AlterPaymentCommand
{
    /** @var OrderInterface */
    private $payment;

    public function __construct(OrderInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getOrder(): OrderInterface
    {
        return $this->payment;
    }
}
