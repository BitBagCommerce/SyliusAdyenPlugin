<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use BitBag\SyliusAdyenPlugin\Bus\Handler\RefundPaymentHandler;
use BitBag\SyliusAdyenPlugin\RefundPaymentTransitions;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethod;
use Sylius\RefundPlugin\Entity\RefundPayment as RefundPaymentEntity;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

class RefundPaymentHandlerTest extends TestCase
{
    use StateMachineTrait;

    /** @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $refundPaymentManager;

    /** @var RefundPaymentHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->setupStateMachineMocks();

        $this->refundPaymentManager = $this->createMock(EntityManagerInterface::class);
        $this->handler = new RefundPaymentHandler($this->stateMachineFactory, $this->refundPaymentManager);
    }

    public function testProcess(): void
    {
        $entity = new RefundPaymentEntity(
            $this->createMock(OrderInterface::class),
            42,
            'EUR',
            RefundPaymentInterface::STATE_NEW,
            new PaymentMethod()
        );

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with(
                $this->equalTo(RefundPaymentTransitions::TRANSITION_CONFIRM)
            )
        ;

        $this->refundPaymentManager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->equalTo($entity)
            )
        ;

        $command = new RefundPayment($entity);
        ($this->handler)($command);
    }
}
