<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentStatusReceived;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Handler\PaymentStatusReceivedHandler;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;

class PaymentStatusReceivedHandlerTest extends TestCase
{
    private const TESTING_RESULT_CODE = 'Chrząszcz';

    use StateMachineTrait;

    /** @var PaymentStatusReceivedHandler */
    private $handler;

    /** @var mixed|\PHPUnit\Framework\MockObject\MockObject|EntityRepository */
    private $paymentRepository;

    /** @var Dispatcher|mixed|\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    /** @var \PHPUnit\Framework\MockObject\MockObject|EntityRepository */
    private $orderRepository;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->paymentRepository = $this->createMock(EntityRepository::class);
        $this->orderRepository = $this->createMock(EntityRepository::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);
        $this->handler = new PaymentStatusReceivedHandler(
            $this->stateMachineFactory,
            $this->paymentRepository,
            $this->orderRepository,
            $this->dispatcher
        );
    }

    public static function provideForTestFlow(): array
    {
        $result = [
            'dummy result code' => [
                self::TESTING_RESULT_CODE, false,
            ],
        ];

        foreach (PaymentStatusReceivedHandler::ALLOWED_EVENT_NAMES as $eventName) {
            $result[sprintf('valid result code: %s', $eventName)] = [
                $eventName, true,
            ];
        }

        return $result;
    }

    /**
     * @dataProvider provideForTestFlow
     */
    public function testFlow(string $resultCode, bool $shouldPass): void
    {
        $order = new Order();

        $payment = new Payment();
        $payment->setDetails([
            'resultCode' => $resultCode,
            'pspReference' => '123',
        ]);
        $order->addPayment($payment);

        $invocation = $shouldPass ? $this->once() : $this->never();
        $this->stateMachine
            ->expects($invocation)
            ->method('apply')
        ;

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
        ;

        $command = new PaymentStatusReceived($payment);
        ($this->handler)($command);
    }
}
