<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentFinalizationCommand;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PaymentFinalizationHandler implements MessageHandlerInterface
{
    use OrderFromPaymentTrait;

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

    private function persistPaymentAndOrder(PaymentInterface $payment): void
    {
        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();

        $this->orderManager->persist($this->getOrderFromPayment($payment));
        $this->orderManager->flush();
    }

    private function updateOrderState(OrderInterface $order, string $transition): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);

        if (!$stateMachine->can($transition)) {
            return;
        }

        $stateMachine->apply($transition);
    }

    public function __invoke(PaymentFinalizationCommand $command): void
    {
        $payment = $command->getPayment();

        if (!$this->isAccepted($payment)) {
            return;
        }

        $payment->setState($command->getTargetPaymentState());
        $this->updateOrderState($this->getOrderFromPayment($payment), $command->getOrderTransition());

        $this->persistPaymentAndOrder($payment);
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        return $this->getOrderFromPayment($payment)->getPaymentState() !== OrderPaymentStates::STATE_PAID;
    }
}
