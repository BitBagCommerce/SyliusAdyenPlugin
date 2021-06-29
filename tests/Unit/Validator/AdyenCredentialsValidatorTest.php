<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Validator;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentials;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentialsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class AdyenCredentialsValidatorTest extends ConstraintValidatorTestCase
{
    /** @var AdyenClientInterface */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(AdyenClientInterface::class);
    }

    protected function createValidator(): AdyenCredentialsValidator
    {
        return new AdyenCredentialsValidator($this->client);
    }

    public function testAffirmative()
    {
        $constraint = new AdyenCredentials();
        $this->validator->validate([], $constraint);

        $this->assertNoViolation();
    }
}
