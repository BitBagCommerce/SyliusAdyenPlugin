<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Exception;

use Throwable;

class PaymentMethodForReferenceNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $reference, Throwable $previous = null)
    {
        parent::__construct(sprintf('Payment not found for reference "%s"', $reference), 0, $previous);
    }
}
