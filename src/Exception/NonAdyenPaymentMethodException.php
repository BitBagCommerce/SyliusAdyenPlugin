<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Exception;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Throwable;

class NonAdyenPaymentMethodException extends \InvalidArgumentException
{
    public function __construct(PaymentMethodInterface $paymentMethod, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Provided PaymentMethod #%d is not an Adyen instance',
                (int) $paymentMethod->getId()
            ),
            0,
            $previous
        );
    }
}
