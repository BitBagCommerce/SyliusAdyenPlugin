<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Handler\RefundPaymentGeneratedHandler;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepository;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;
use Payum\Core\Model\GatewayConfig;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

class RefundPaymentGeneratedHandlerTest extends TestCase
{
    private const DUMMY_REFERENCE = 'W Szczebrzeszynie chrząszcz brzmi w trzcinie';

    private const PSP_REFERENCE = 'Bakłażan';

    use AdyenClientTrait;

    /** @var RefundReferenceResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $refundReferenceResolver;

    /** @var PaymentRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentRepository;

    /** @var PaymentMethodRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentMethodRepository;

    /** @var RefundPaymentGeneratedHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->setupAdyenClientMocks();

        $this->refundReferenceResolver = $this->createMock(RefundReferenceResolver::class);
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepository::class);

        $this->handler = new RefundPaymentGeneratedHandler(
            $this->refundReferenceResolver,
            $this->adyenClientProvider,
            $this->paymentRepository,
            $this->paymentMethodRepository
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

        $payment = new Payment();
        $payment->setMethod($paymentMethod);
        $payment->setDetails([
            'pspReference' => self::PSP_REFERENCE
        ]);

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

        $this->refundReferenceResolver
            ->method('createReference')
            ->willReturn(self::DUMMY_REFERENCE)
            ->with(
                $this->equalTo($command->orderNumber()),
                $this->equalTo($command->id())
            )
        ;

        $this->adyenClient
            ->expects($this->once())
            ->method('requestRefund')
            ->with(
                $this->equalTo(self::PSP_REFERENCE),
                $this->equalTo($command->amount()),
                $this->equalTo($command->currencyCode()),
                $this->equalTo(self::DUMMY_REFERENCE)
            )
        ;

        ($this->handler)($command);
    }
}
