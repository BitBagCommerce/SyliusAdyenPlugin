<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ProvinceAddressConstraintValidatorDecorator extends ConstraintValidator
{
    public const PROVINCE_REQUIRED_COUNTRIES_DEFAULT_LIST = [
        'CA', 'US',
    ];

    /** @var ProvinceAddressConstraintValidator */
    private $decorated;

    /** @var array|string[] */
    private $provinceRequiredCountriesList;

    public function __construct(
        ProvinceAddressConstraintValidator $decorated,
        array $provinceRequiredCountriesList = self::PROVINCE_REQUIRED_COUNTRIES_DEFAULT_LIST
    ) {
        $this->decorated = $decorated;
        $this->provinceRequiredCountriesList = $provinceRequiredCountriesList;
    }

    /**
     * @param AddressInterface|mixed $value
     * @param ProvinceAddressConstraint|Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $this->decorated->initialize($this->context);
        $this->decorated->validate($value, $constraint);

        Assert::isInstanceOf($value, AddressInterface::class);
        Assert::isInstanceOf($constraint, ProvinceAddressConstraint::class);

        if ($this->hasViolation($constraint)) {
            return;
        }

        if (!in_array((string) $value->getCountryCode(), $this->provinceRequiredCountriesList, true)) {
            return;
        }

        if (null !== $value->getProvinceCode() || null !== $value->getProvinceName()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }

    private function hasViolation(Constraint $constraint): bool
    {
        Assert::isInstanceOf($constraint, ProvinceAddressConstraint::class);

        foreach ($this->context->getViolations() as $violation) {
            if ($violation->getMessageTemplate() === $constraint->message) {
                return true;
            }
        }

        return false;
    }
}
