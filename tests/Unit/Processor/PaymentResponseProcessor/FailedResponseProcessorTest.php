<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\PaymentResponseProcessor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\FailedResponseProcessor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\RequestMother;

class FailedResponseProcessorTest extends AbstractProcessorTest
{
    private const TOKEN_VALUE = 'Szczebrzeszyn';

    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new FailedResponseProcessor(
            self::getRouter(self::$container),
            self::$container->get('translator')
        );
    }

    public function testProcess(): void
    {
        $payment = $this->getPayment('authorized', self::TOKEN_VALUE);

        $request = RequestMother::createWithSession();

        /**
         * @var $result RedirectResponse
         */
        $result = $this->processor->process('code', $request, $payment);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertStringEndsWith(self::TOKEN_VALUE, $result->getTargetUrl());
        $this->assertNotEmpty($request->getSession()->getFlashBag()->get('error'));
    }

    public static function provideForTestAccepts(): array
    {
        return [
            'affirmative' => ['authorized', false],
            'negative' => ['refused', true],
        ];
    }
}
