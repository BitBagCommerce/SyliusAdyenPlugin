<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\AuthorizePayment;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AuthorizePaymentHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EntityManagerInterface */
    private $orderManager;

    /** @var EntityManagerInterface */
    private $paymentManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $orderManager,
        EntityManagerInterface $paymentManager
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
        $this->paymentManager = $paymentManager;
    }

    private function processOrder(PaymentInterface $payment)
    {
        if (!$this->isAccepted($payment)) {
            return;
        }

        $payment->setState(PaymentInterface::STATE_COMPLETED);
        $order = $payment->getOrder();

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        if ($stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)) {
            $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);
        }

        $this->orderManager->persist($order);
        $this->orderManager->flush();
    }

    public function __invoke(AuthorizePayment $command)
    {
        $this->processOrder($command->getPayment());
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        return $payment->getOrder()->getPaymentState() !== OrderPaymentStates::STATE_PAID;
    }
}
