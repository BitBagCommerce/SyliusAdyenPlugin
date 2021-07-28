<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Bus\Handler\PreparePaymentHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;

class PreparePaymentHandlerTest extends TestCase
{
    private const TESTING_RESULT_CODE = 'Chrząszcz';

    use StateMachineTrait;

    /** @var PreparePaymentHandler */
    private $handler;

    /** @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentManager;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->paymentManager = $this->createMock(EntityManagerInterface::class);
        $this->handler = new PreparePaymentHandler($this->stateMachineFactory, $this->paymentManager);
    }

    public static function provideForTestFlow(): array
    {
        $result = [
            'dummy result code' => [
                self::TESTING_RESULT_CODE, false
            ]
        ];

        foreach (PreparePaymentHandler::ALLOWED_EVENT_NAMES as $eventName) {
            $result[sprintf('valid result code: %s', $eventName)] = [
                $eventName, true
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
            'resultCode' => $resultCode
        ]);
        $order->addPayment($payment);

        $this->stateMachine
            ->expects(
                $shouldPass ? $this->once() : $this->never()
            )
            ->method('apply')
        ;

        $command = new PreparePayment($payment);
        ($this->handler)($command);
    }
}
