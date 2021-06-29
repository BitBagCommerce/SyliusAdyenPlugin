<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PreparePaymentHandler implements MessageHandlerInterface
{
    use OrderFromPaymentTrait;

    public const ALLOWED_EVENT_NAMES = ['Authorised', 'RedirectShopper'];

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var EntityManagerInterface */
    private $paymentManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $paymentManager
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentManager = $paymentManager;
    }

    private function updateOrderState(OrderInterface $order): void
    {
        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        if (!$sm->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            return;
        }

        $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function persistPayment(PaymentInterface $payment): void
    {
        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();
    }

    public function __invoke(PreparePayment $command): void
    {
        $payment = $command->getPayment();
        if ($this->isAccepted($payment)) {
            $this->updateOrderState($this->getOrderFromPayment($payment));
        }

        $this->persistPayment($payment);
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        $details = $payment->getDetails();

        return in_array($details['resultCode'], self::ALLOWED_EVENT_NAMES, true);
    }
}
