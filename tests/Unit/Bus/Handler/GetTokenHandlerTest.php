<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateToken;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Handler\GetTokenHandler;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenToken;
use BitBag\SyliusAdyenPlugin\Repository\AdyenTokenRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class GetTokenHandlerTest extends TestCase
{
    /** @var AdyenTokenRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $adyenTokenRepository;

    /** @var Dispatcher|\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    /** @var GetTokenHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->adyenTokenRepository = $this->createMock(AdyenTokenRepositoryInterface::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->handler = new GetTokenHandler($this->adyenTokenRepository, $this->dispatcher);
    }

    public function testForTokenWithoutCustomer(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $token = new GetToken(
            $this->createMock(PaymentMethodInterface::class),
            $this->createMock(OrderInterface::class)
        );
        ($this->handler)($token);
    }

    public static function provideForTestQuery(): array
    {
        return [
            'for already existing' => [
                true
            ],
            'for non-existing' => [
                false
            ]
        ];
    }

    private function setupMocks(bool $existingToken, PaymentMethodInterface $paymentMethod, CustomerInterface $customer)
    {
        $repositoryMethod = $this->adyenTokenRepository
            ->method('findOneByPaymentMethodAndCustomer')
            ->with($this->equalTo($paymentMethod), $this->equalTo($customer))
        ;

        if ($existingToken) {
            $repositoryMethod->willReturn(new AdyenToken());

            $this->dispatcher
                ->expects($this->never())
                ->method('dispatch');

            return;
        }

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (CreateToken $command) use ($paymentMethod, $customer) {
                return $command->getPaymentMethod() === $paymentMethod
                    && $command->getCustomer() === $customer;
            }))
            ->willReturn(new AdyenToken())
        ;
    }

    /**
     * @dataProvider provideForTestQuery
     */
    public function testQuery(bool $existingToken = false): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $order
            ->method('getCustomer')
            ->willReturn($customer)
        ;

        $query = new GetToken($paymentMethod, $order);

        $this->setupMocks($existingToken, $paymentMethod, $customer);

        $result = ($this->handler)($query);
        $this->assertInstanceOf(AdyenToken::class, $result);
    }
}
