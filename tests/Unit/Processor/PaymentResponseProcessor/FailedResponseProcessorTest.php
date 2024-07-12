<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\PaymentResponseProcessor;

use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\FailedResponseProcessor;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Mock\RequestMother;

class FailedResponseProcessorTest extends AbstractProcessorTest
{
    private const TOKEN_VALUE = 'Szczebrzeszyn';

    /** @var DispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = $this->createMock(DispatcherInterface::class);

        $this->processor = new FailedResponseProcessor(
            self::getRouter($this->getContainer()),
            $this->getContainer()->get('translator'),
            $this->dispatcher,
        );
    }

    public function testProcess(): void
    {
        $payment = $this->getPayment('authorized', self::TOKEN_VALUE);

        $request = RequestMother::createWithSession();

        $result = $this->processor->process('code', $request, $payment);

        $this->assertStringEndsWith(self::TOKEN_VALUE, $result);
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
