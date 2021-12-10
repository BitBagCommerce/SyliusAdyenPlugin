<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Validator;

use BitBag\SyliusAdyenPlugin\Validator\Constraint\ProvinceAddressConstraintValidatorDecorator;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\BitBag\SyliusAdyenPlugin\Unit\AddressMother;

class ProvinceAddressConstraintValidatorDecoratorTest extends ConstraintValidatorTestCase
{
    /** @var mixed|\PHPUnit\Framework\MockObject\MockObject|ProvinceAddressConstraintValidator */
    private $decorated;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ProvinceAddressConstraintValidator::class);

        parent::setUp();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ProvinceAddressConstraintValidatorDecorator($this->decorated);
    }

    public function testNonRelatedCountry(): void
    {
        $constraint = new ProvinceAddressConstraint();
        $address = AddressMother::createShippingAddress();

        $this->validator->validate($address, $constraint);
        $this->assertNoViolation();
    }

    public function testRelatedCountryAndEmptyProvince(): void
    {
        $constraint = new ProvinceAddressConstraint();
        $address = AddressMother::createAddressWithSpecifiedCountryAndEmptyProvince('US');

        $this->validator->validate($address, $constraint);
        $this->buildViolation($constraint->message)
            ->assertRaised()
        ;
    }

    public static function provideTestRelatedCountryAndEmptyProvinceWithAlreadyViolatedConstraint(): array
    {
        $constraint = new ProvinceAddressConstraint();

        return [
            'with foreign constraint' => ['some foreign constraint', 2],
            'with decorated constraint' => [$constraint->message, 1],
        ];
    }

    /**
     * @dataProvider provideTestRelatedCountryAndEmptyProvinceWithAlreadyViolatedConstraint
     */
    public function testRelatedCountryAndEmptyProvinceWithAlreadyViolatedConstraint(
        string $violationMessage,
        int $expectedCount
    ): void {
        $constraint = new ProvinceAddressConstraint();
        $address = AddressMother::createAddressWithSpecifiedCountryAndEmptyProvince('US');
        $this->context->addViolation($violationMessage);

        $this->validator->validate($address, $constraint);
        $this->assertCount($expectedCount, $this->context->getViolations());
    }
}
