<?php


namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;


use Symfony\Component\Validator\Constraint;

class AdyenCredentials extends Constraint
{
    public function validatedBy()
    {
        return 'bit_bag.sylius_adyen_plugin.validator.constraint.adyen_credentials_validator';
    }

}