<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Validator;

use Adyen\HttpClient\ClientInterface;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Client\AdyenTransportFactory;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentials;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentialsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class AdyenCredentialsValidatorTest extends ConstraintValidatorTestCase
{
    /** @var ClientInterface */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        parent::setUp();
    }

    protected function createValidator(): AdyenCredentialsValidator
    {
        return new AdyenCredentialsValidator(
            new AdyenTransportFactory($this->client)
        );
    }

    public function testAffirmative()
    {
        $constraint = new AdyenCredentials();
        $this->validator->validate([
            'environment' => AdyenClientInterface::TEST_ENVIRONMENT,
            'merchantAccount' => 'mer',
            'apiKey' => 'api'
        ], $constraint);

        $this->assertNoViolation();
    }
}
