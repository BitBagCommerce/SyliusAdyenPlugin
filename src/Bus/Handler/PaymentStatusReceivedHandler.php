<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateReferenceForPayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentStatusReceived;
use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PaymentStatusReceivedHandler implements MessageHandlerInterface
{
    use OrderFromPaymentTrait;

    public const ALLOWED_EVENT_NAMES = ['authorised', 'redirectshopper'];

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var RepositoryInterface */
    private $paymentRepository;

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var RepositoryInterface */
    private $orderRepository;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        RepositoryInterface $paymentRepository,
        RepositoryInterface $orderRepository,
        DispatcherInterface $dispatcher
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentRepository = $paymentRepository;
        $this->dispatcher = $dispatcher;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(PaymentStatusReceived $command): void
    {
        $payment = $command->getPayment();

        if ($this->isAccepted($payment)) {
            $this->updateOrderState($this->getOrderFromPayment($payment));
        }

        try {
            $this->dispatcher->dispatch(new CreateReferenceForPayment($payment));
            $this->paymentRepository->add($payment);
        } catch (\InvalidArgumentException $ex) {
            // probably redirect, we don't have a pspReference at this stage
        }
    }

    private function updateOrderState(OrderInterface $order): void
    {
        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE, true);

        $this->orderRepository->add($order);
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        $details = $payment->getDetails();

        $resultCode = strtolower((string) $details['resultCode']);

        return in_array($resultCode, self::ALLOWED_EVENT_NAMES, true);
    }
}
