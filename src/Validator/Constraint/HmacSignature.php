<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class HmacSignature extends Constraint
{
    /** @var string */
    public $message = 'bitbag_sylius_adyen_plugin.runtime.signature';

    public function getTargets()
    {
        return parent::CLASS_CONSTRAINT;
    }
}
