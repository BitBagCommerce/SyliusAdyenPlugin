<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
            new AdyenTransportFactory(null, $this->client),
        );
    }

    public function testAffirmative(): void
    {
        $constraint = new AdyenCredentials();
        $this->validator->validate([
            'environment' => AdyenClientInterface::TEST_ENVIRONMENT,
            'merchantAccount' => 'mer',
            'apiKey' => 'api',
            'liveEndpointUrlPrefix' => 'prefix',
        ], $constraint);

        $this->assertNoViolation();
    }
}
