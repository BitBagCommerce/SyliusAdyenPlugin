<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Processor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor;
use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\ProcessorInterface;
use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Processor\PaymentResponseProcessor\AbstractProcessor;

class PaymentResponseProcessorTest extends KernelTestCase
{
    private const URL_ENDING = 'thank-you';

    protected function setUp(): void
    {
        self::bootKernel();
    }

    private function getPaymentResponseProcessor(array $processors = []): PaymentResponseProcessorInterface
    {
        return new PaymentResponseProcessor(
            $processors,
            AbstractProcessor::getRouter($this->getContainer()),
        );
    }

    private function getProcessor(bool $accepts, ?string $response = null): ProcessorInterface
    {
        $result = $this->createMock(ProcessorInterface::class);
        if ($accepts) {
            $result
                ->method('accepts')
                ->willReturn($accepts)
            ;
        }

        if (null !== $response) {
            $result
                ->method('process')
                ->willReturn($response)
            ;
        }

        return $result;
    }

    public function testForNoAcceptingProcessor(): void
    {
        $tested = $this->getPaymentResponseProcessor([$this->getProcessor(false)]);

        $result = $tested->process('code', Request::create('/'), null);

        $this->assertStringEndsWith(self::URL_ENDING, $result);
    }

    public function testAcceptingProcessor(): void
    {
        $payment = $this->createMock(PaymentInterface::class);

        $tested = $this->getPaymentResponseProcessor([$this->getProcessor(true, self::URL_ENDING)]);

        $result = $tested->process('code', Request::create('/'), $payment);
        $this->assertEquals(self::URL_ENDING, $result);
    }
}
