<?php


namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AdyenCredentialsValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        return;
    }
}