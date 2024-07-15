<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\RefundPayment;
use BitBag\SyliusAdyenPlugin\RefundPaymentTransitions as BitBagRefundPaymentTransitions;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;

final class RefundPaymentHandler
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EntityManagerInterface */
    private $refundPaymentManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $refundPaymentManager,
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->refundPaymentManager = $refundPaymentManager;
    }

    public function __invoke(RefundPayment $command): void
    {
        $machine = $this->stateMachineFactory->get($command->getRefundPayment(), RefundPaymentTransitions::GRAPH);
        $machine->apply(BitBagRefundPaymentTransitions::TRANSITION_CONFIRM, true);

        $this->refundPaymentManager->persist($command->getRefundPayment());
        $this->refundPaymentManager->flush();
    }
}
