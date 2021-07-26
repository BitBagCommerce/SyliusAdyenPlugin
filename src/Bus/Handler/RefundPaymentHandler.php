<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RefundPaymentHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EntityManagerInterface */
    private $refundPaymentManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $refundPaymentManager
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->refundPaymentManager = $refundPaymentManager;
    }

    public function __invoke(RefundPayment $command): void
    {
        $machine = $this->stateMachineFactory->get($command->getRefundPayment(), RefundPaymentTransitions::GRAPH);
        $machine->apply('confirm', true);

        $this->refundPaymentManager->persist($command->getRefundPayment());
        $this->refundPaymentManager->flush();
    }
}
