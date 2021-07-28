<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Bus\Handler\TakeOverPaymentHandler;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class TakeOverPaymentHandlerTest extends TestCase
{
    private const TEST_PAYMENT_CODE = 'Bakłażan';

    private const NEW_TEST_PAYMENT_CODE = 'Szczebrzeszyn';

    /** @var PaymentMethodRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentMethodRepository;

    /** @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentManager;

    /** @var TakeOverPaymentHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new TakeOverPaymentHandler(
            $this->paymentMethodRepository,
            $this->paymentManager
        );
    }

    public function testTheSamePaymentMethod(): void
    {
        $this->paymentMethodRepository
            ->expects($this->never())
            ->method('getOneForAdyenAndCode')
        ;

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setCode(self::TEST_PAYMENT_CODE);

        $command = new TakeOverPayment(
            $this->createPayment($paymentMethod)->getOrder(),
            self::TEST_PAYMENT_CODE
        );
        ($this->handler)($command);
    }

    public function testChange(): void
    {
        $this->paymentManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->isInstanceOf(PaymentInterface::class)
            )
        ;

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setCode(self::TEST_PAYMENT_CODE);

        $newPaymentMethod = new PaymentMethod();
        $newPaymentMethod->setCode(self::NEW_TEST_PAYMENT_CODE);

        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('getOneForAdyenAndCode')
            ->with(
                $this->equalTo(self::NEW_TEST_PAYMENT_CODE)
            )
            ->willReturn($newPaymentMethod)
        ;

        $payment = $this->createPayment($paymentMethod);
        $command = new TakeOverPayment($payment->getOrder(), self::NEW_TEST_PAYMENT_CODE);

        ($this->handler)($command);

        $this->assertEquals($newPaymentMethod, $payment->getMethod());
    }

    private function createPayment(PaymentMethodInterface $paymentMethod): PaymentInterface
    {
        $order = new Order();
        $payment = new Payment();
        $payment->setMethod($paymentMethod);
        $payment->setState(PaymentInterface::STATE_NEW);

        $order->addPayment($payment);

        return $payment;
    }
}
