<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class AdyenCredentials extends Constraint
{
    public function validatedBy()
    {
        return 'bit_bag_sylius_adyen_plugin_credentials';
    }
}
