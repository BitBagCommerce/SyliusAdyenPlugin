<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\CapturePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentFinalizationCommand;
use BitBag\SyliusAdyenPlugin\Bus\Handler\PaymentFinalizationHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\OrderPaymentStates;

class PaymentFinalizationHandlerTest extends TestCase
{
    use StateMachineTrait;

    /** @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $orderManager;

    /** @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentManager;

    /** @var PaymentFinalizationHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->orderManager = $this->createMock(EntityManagerInterface::class);
        $this->paymentManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new PaymentFinalizationHandler(
            $this->stateMachineFactory,
            $this->orderManager,
            $this->paymentManager
        );
    }

    public function testUnacceptable(): void
    {
        $order = new Order();
        $order->setPaymentState(OrderPaymentStates::STATE_PAID);

        $payment = new Payment();
        $payment->setOrder($order);

        $this->paymentManager
            ->expects($this->never())
            ->method('persist')
        ;

        $command = new AuthorizePayment($payment);
        ($this->handler)($command);
    }

    public static function provideForTestForApplicable(): array
    {
        return [
            'capture action' => [
                CapturePayment::class
            ],
            'authorize action' => [
                AuthorizePayment::class
            ]
        ];
    }

    /**
     * @dataProvider provideForTestForApplicable
     */
    public function testApplicable(string $class): void
    {
        $order = new Order();
        $order->setPaymentState(OrderPaymentStates::STATE_AUTHORIZED);

        $payment = new Payment();
        $payment->setOrder($order);

        /**
         * @var PaymentFinalizationCommand $command
         */
        $command = new $class($payment);

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($command->getPaymentTransition()))
        ;

        $this
            ->paymentManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->equalTo($payment)
            )
        ;

        $this
            ->orderManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->equalTo($order)
            )
        ;

        ($this->handler)($command);
    }
}
