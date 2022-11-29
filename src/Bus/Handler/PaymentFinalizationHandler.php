<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentFinalizationCommand;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\ApiBundle\Command\SendOrderConfirmation;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class PaymentFinalizationHandler implements MessageHandlerInterface
{
    use OrderFromPaymentTrait;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var RepositoryInterface */
    private $orderRepository;

    private MessageBusInterface $commandBus;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        RepositoryInterface $orderRepository,
        MessageBusInterface $commandBus
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderRepository = $orderRepository;
        $this->commandBus = $commandBus;
    }

    private function updatePaymentState(PaymentInterface $payment, string $transition): void
    {
        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $stateMachine->apply($transition, true);
    }

    private function updatePayment(PaymentInterface $payment): void
    {
        $order = $payment->getOrder();
        if (null === $order) {
            return;
        }

        $this->orderRepository->add($order);
    }

    public function __invoke(PaymentFinalizationCommand $command): void
    {
        $payment = $command->getPayment();

        if (!$this->isAccepted($payment)) {
            return;
        }
        $order = $payment->getOrder();
        $this->updatePaymentState($payment, $command->getPaymentTransition());
        if (null !== $order){
            $this->commandBus->dispatch(new SendOrderConfirmation($order->getTokenValue()));
        }

        $this->updatePayment($payment);
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        return OrderPaymentStates::STATE_PAID !== $this->getOrderFromPayment($payment)->getPaymentState();
    }
}
