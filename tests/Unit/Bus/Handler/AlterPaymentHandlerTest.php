<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\AlterPaymentCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\CancelPayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\RequestCapture;
use BitBag\SyliusAdyenPlugin\Bus\Handler\AlterPaymentHandler;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Payum\Core\Model\GatewayConfig;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;

class AlterPaymentHandlerTest extends TestCase
{
    private const PSP_REFERENCE = 'Szczebrzeszyn';

    private const ORDER_CURRENCY_CODE = 'EUR';

    private const ORDER_AMOUNT = 42;

    use AdyenClientTrait;

    /** @var AlterPaymentHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->setupAdyenClientMocks();
        $this->handler = new AlterPaymentHandler($this->adyenClientProvider);
    }

    public static function provideForTestForNonApplicablePayment(): array
    {
        $paymentWithoutMethod = new Payment();

        $methodWithEmptyConfig = new PaymentMethod();
        $paymentWithEmptyConfig = new Payment();
        $paymentWithEmptyConfig->setMethod($methodWithEmptyConfig);

        $config = new GatewayConfig();
        $config->setConfig(['blah' => 1]);
        $nonAdyenPaymentMethod = new PaymentMethod();
        $nonAdyenPaymentMethod->setGatewayConfig($config);
        $paymentWithoutAdyenConfiguration = new Payment();
        $paymentWithoutAdyenConfiguration->setState(PaymentInterface::STATE_AUTHORIZED);
        $paymentWithoutAdyenConfiguration->setMethod($nonAdyenPaymentMethod);

        $paymentWithoutReference = clone $paymentWithoutAdyenConfiguration;
        $paymentWithoutReference->getMethod()->getGatewayConfig()->setConfig([
            AdyenClientProvider::FACTORY_NAME => 1,
        ]);

        return [
            'payment not found' => [null],
            'payment without method' => [$paymentWithoutMethod],
            'payment method without configuration' => [$paymentWithEmptyConfig],
            'payment for non-Adyen configuration' => [$paymentWithoutAdyenConfiguration],
            'payment without pspReference' => [$paymentWithoutReference],
            'completed order' => [$paymentWithoutAdyenConfiguration, PaymentInterface::STATE_COMPLETED],
        ];
    }

    /**
     * @dataProvider provideForTestForNonApplicablePayment
     */
    public function testForNonApplicablePayment(?PaymentInterface $payment = null, ?string $orderPaymentState = null): void
    {
        $this->adyenClientProvider
            ->expects($this->never())
            ->method('getForPaymentMethod')
        ;

        $order = new Order();
        if ($orderPaymentState !== null) {
            $order->setPaymentState($orderPaymentState);
        }

        if ($payment !== null) {
            $order->addPayment($payment);
        }

        $command = $this->createMock(AlterPaymentCommand::class);
        $command
            ->method('getOrder')
            ->willReturn($order)
        ;

        ($this->handler)($command);
    }

    public static function provideForTestForValidPayment(): array
    {
        return [
            'request capture' => [
                RequestCapture::class,
                function () {
                    $this
                        ->adyenClient
                        ->expects($this->once())
                        ->method('requestCapture')
                        ->with(
                            $this->equalTo(self::PSP_REFERENCE),
                            $this->equalTo(self::ORDER_AMOUNT),
                            $this->equalTo(self::ORDER_CURRENCY_CODE)
                        )
                    ;
                },
            ],
            'cancel payment' => [
                CancelPayment::class,
                function () {
                    $this
                        ->adyenClient
                        ->expects($this->once())
                        ->method('requestCancellation')
                        ->with(
                            $this->equalTo(self::PSP_REFERENCE)
                        )
                    ;
                },
            ],
        ];
    }

    /**
     * @dataProvider provideForTestForValidPayment
     */
    public function testForValidPayment(string $commandClass, callable $setupMocker): void
    {
        $config = new GatewayConfig();
        $config->setConfig([
            AdyenClientProvider::FACTORY_NAME => 1,
        ]);

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setGatewayConfig($config);

        $payment = new Payment();
        $payment->setState(PaymentInterface::STATE_AUTHORIZED);
        $payment->setMethod($paymentMethod);
        $payment->setDetails(['pspReference' => self::PSP_REFERENCE]);

        $order = new Order();
        $order->addPayment($payment);
        $order->setCurrencyCode(self::ORDER_CURRENCY_CODE);

        $item = new OrderItem();
        $item->setUnitPrice(self::ORDER_AMOUNT);
        $item->setOrder($order);

        $unit = new OrderItemUnit($item);

        $setupMocker->bindTo($this)();

        /**
         * @var AlterPaymentCommand $command
         */
        $command = new $commandClass($order);

        ($this->handler)($command);
    }
}
