<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\MarkOrderAsCompleted;
use BitBag\SyliusAdyenPlugin\Bus\Handler\MarkOrderAsCompletedHandler;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;

class PreparePaymentHandlerTest extends TestCase
{
    private const TESTING_RESULT_CODE = 'Chrząszcz';

    use StateMachineTrait;

    /** @var MarkOrderAsCompletedHandler */
    private $handler;

    /** @var mixed|\PHPUnit\Framework\MockObject\MockObject|EntityRepository */
    private $paymentRepository;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->paymentRepository = $this->createMock(EntityRepository::class);
        $this->handler = new MarkOrderAsCompletedHandler($this->stateMachineFactory, $this->paymentRepository);
    }

    public static function provideForTestFlow(): array
    {
        $result = [
            'dummy result code' => [
                self::TESTING_RESULT_CODE, false
            ]
        ];

        foreach (MarkOrderAsCompletedHandler::ALLOWED_EVENT_NAMES as $eventName) {
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

        $command = new MarkOrderAsCompleted($payment);
        ($this->handler)($command);
    }
}
