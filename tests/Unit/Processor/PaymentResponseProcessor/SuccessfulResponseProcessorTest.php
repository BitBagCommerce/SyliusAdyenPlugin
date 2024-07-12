<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\PaymentResponseProcessor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\SuccessfulResponseProcessor;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Mock\RequestMother;

class SuccessfulResponseProcessorTest extends AbstractProcessorTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new SuccessfulResponseProcessor(
            $this->getContainer()->get('tests.bitbag.sylius_adyen_plugin.bus.dispatcher'),
            self::getRouter($this->getContainer()),
            $this->getContainer()->get('translator'),
        );
    }

    public static function provideForTestAccepts(): array
    {
        return [
            'affirmative' => ['authorised', true],
            'negative' => ['refused', false],
        ];
    }

    public static function provideForTestRedirect(): array
    {
        return [
            'generic' => [
                RequestMother::createWithSessionForDefinedOrderId(),
                'thank-you',
            ],
            'alternative' => [
                RequestMother::createWithSessionForSpecifiedQueryToken(),
                '/orders/',
                true,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestRedirect
     */
    public function testRedirect(
        Request $request,
        string $expectedUrlEnding,
        bool $expectFlash = false,
    ) {
        $payment = $this->createMock(PaymentInterface::class);

        $result = $this->processor->process('Szczebrzeszyn', $request, $payment);

        $this->assertIsPaymentScheduledForFinalization();
        $this->assertStringEndsWith($expectedUrlEnding, $result);

        if (!$expectFlash) {
            return;
        }

        $this->assertNotEmpty($request->getSession()->getFlashbag()->get('info'));
    }

    private function assertIsPaymentScheduledForFinalization(): void
    {
        $messenger = $this->getContainer()->get('tests.bitbag.sylius_adyen_plugin.message_bus');
        $commands = $messenger->getDispatchedMessages();

        $this->assertNotEmpty($commands);
    }
}
