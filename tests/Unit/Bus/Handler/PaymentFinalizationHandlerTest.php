<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\CapturePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentFinalizationCommand;
use BitBag\SyliusAdyenPlugin\Bus\Handler\PaymentFinalizationHandler;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\OrderPaymentStates;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentFinalizationHandlerTest extends TestCase
{
    use StateMachineTrait;

    /** @var PaymentFinalizationHandler */
    private $handler;

    /** @var mixed|\PHPUnit\Framework\MockObject\MockObject|EntityRepository */
    private $orderRepository;

    /** @var mixed|\Symfony\Component\Messenger\MessageBusInterface */
    private $commandBus;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->orderRepository = $this->createMock(EntityRepository::class);

        $this->commandBus = $this->createMock(MessageBusInterface::class);

        $this->handler = new PaymentFinalizationHandler(
            $this->stateMachineFactory,
            $this->orderRepository,
            $this->commandBus,
        );
    }

    public function testUnacceptable(): void
    {
        $order = new Order();
        $order->setPaymentState(OrderPaymentStates::STATE_PAID);

        $payment = new Payment();
        $payment->setOrder($order);

        $this->orderRepository
            ->expects($this->never())
            ->method('add')
        ;

        $command = new AuthorizePayment($payment);
        ($this->handler)($command);
    }

    public static function provideForTestForApplicable(): array
    {
        return [
            'capture action' => [
                CapturePayment::class,
            ],
            'authorize action' => [
                AuthorizePayment::class,
            ],
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
            ->orderRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo($order)
            )
        ;

        ($this->handler)($command);
    }
}
