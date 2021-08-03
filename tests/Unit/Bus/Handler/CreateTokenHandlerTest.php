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
use BitBag\SyliusAdyenPlugin\Bus\Handler\CreateTokenHandler;
use BitBag\SyliusAdyenPlugin\Entity\AdyenToken;
use BitBag\SyliusAdyenPlugin\Factory\AdyenTokenFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class CreateTokenHandlerTest extends TestCase
{
    /** @var AdyenTokenFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $adyenTokenFactory;

    /** @var CreateTokenHandler */
    private $handler;
    /**
     * @var mixed|\PHPUnit\Framework\MockObject\MockObject|EntityRepository
     */
    private $tokenRepository;

    protected function setUp(): void
    {
        $this->adyenTokenFactory = $this->createMock(AdyenTokenFactoryInterface::class);
        $this->tokenRepository = $this->createMock(EntityRepository::class);
        $this->handler = new CreateTokenHandler($this->adyenTokenFactory, $this->tokenRepository);
    }

    public function testProcess(): void
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $request = new CreateToken($paymentMethod, $customer);
        $token = new AdyenToken();

        $this->adyenTokenFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($paymentMethod),
                $this->equalTo($customer)
            )
            ->willReturn($token)
        ;

        $this->tokenRepository
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo($token)
            )
        ;

        $result = ($this->handler)($request);
        $this->assertEquals($token, $result);
    }
}
