<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateReferenceForRefund;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Handler\RefundPaymentGeneratedHandler;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepository;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\RefundPaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;
use Payum\Core\Model\GatewayConfig;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Webmozart\Assert\Assert;

class RefundPaymentGeneratedHandlerTest extends TestCase
{
    private const DUMMY_REFERENCE = 'W Szczebrzeszynie chrząszcz brzmi w trzcinie';

    private const PSP_REFERENCE = 'Bakłażan';

    private const NEW_PSP_REFERENCE = 'Rzeżucha';

    use AdyenClientTrait;

    /** @var RefundReferenceResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $refundReferenceResolver;

    /** @var PaymentRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentRepository;

    /** @var PaymentMethodRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentMethodRepository;

    /** @var RefundPaymentGeneratedHandler */
    private $handler;
    /**
     * @var RefundPaymentRepositoryInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $refundPaymentRepository;
    /**
     * @var Dispatcher|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->setupAdyenClientMocks();

        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepository::class);
        $this->refundPaymentRepository = $this->createMock(RefundPaymentRepositoryInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->handler = new RefundPaymentGeneratedHandler(
            $this->adyenClientProvider,
            $this->paymentRepository,
            $this->paymentMethodRepository,
            $this->refundPaymentRepository,
            $this->dispatcher
        );
    }

    public static function provideForTestUnacceptable(): array
    {
        $paymentWithoutPaymentMethod = new Payment();

        $config = new GatewayConfig();
        $nonAdyenPaymentMethod = new PaymentMethod();
        $nonAdyenPaymentMethod->setGatewayConfig($config);
        $paymentWithNonAdyenPaymentMethod = new Payment();
        $paymentWithNonAdyenPaymentMethod->setMethod($nonAdyenPaymentMethod);

        return [
            'no payment provided' => [
                null
            ],
            'payment without payment method' => [
                $paymentWithoutPaymentMethod
            ],
            'payment method non-Adyen' => [
                $paymentWithNonAdyenPaymentMethod
            ]
        ];
    }

    /**
     * @dataProvider provideForTestUnacceptable
     */
    public function testUnacceptable(?PaymentInterface $payment = null): void
    {
        $this->paymentRepository
            ->method('find')
            ->willReturn($payment);

        if ($payment !== null) {
            $this->paymentMethodRepository
                ->method('find')
                ->willReturn($payment->getMethod())
            ;
        }

        ($this->handler)(
            new RefundPaymentGenerated(
                1,
                'Brzęczyszczykiewicz',
                10,
                'EUR',
                1,
                1
            )
        );

        $this->adyenClientProvider
            ->expects($this->never())
            ->method('getForPaymentMethod')
        ;
    }

    public function testAffirmative(): void
    {
        $config = new GatewayConfig();
        $config->setConfig(['adyen' => 1]);

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setGatewayConfig($config);

        $order = new Order();
        $order->setNumber(self::DUMMY_REFERENCE);

        $payment = new Payment();
        $payment->setMethod($paymentMethod);
        $payment->setDetails([
            'pspReference' => self::PSP_REFERENCE
        ]);
        $payment->setOrder($order);

        $this->paymentRepository
            ->method('find')
            ->willReturn($payment)
        ;

        $this->paymentMethodRepository
            ->method('find')
            ->willReturn($paymentMethod)
        ;

        $command = new RefundPaymentGenerated(
            42,
            'blah',
            4242,
            'EUR',
            1,
            1
        );

        $this->adyenClient
            ->expects($this->once())
            ->method('requestRefund')
            ->with(
                $this->equalTo(self::PSP_REFERENCE),
                $this->equalTo($command->amount()),
                $this->equalTo($command->currencyCode()),
                $this->equalTo(self::DUMMY_REFERENCE)
            )
            ->willReturn([
                'pspReference' => self::NEW_PSP_REFERENCE
            ])
        ;

        $this->refundPaymentRepository
            ->method('find')
            ->willReturn($this->createMock(RefundPayment::class))
        ;

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function(CreateReferenceForRefund $command){
                return $command->getRefundReference() === self::NEW_PSP_REFERENCE;
            }));

        ($this->handler)($command);
    }
}
