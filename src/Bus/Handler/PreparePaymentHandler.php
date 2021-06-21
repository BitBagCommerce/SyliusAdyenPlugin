<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;

class PreparePaymentHandler
{
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

    private function modifyPayment(PaymentInterface $payment)
    {
        if (!$this->isAccepted($payment)) {
            return;
        }

        $order = $payment->getOrder();

        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        if (!$sm->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            return;
        }

        $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    public function __invoke(PreparePayment $command)
    {
        $payment = $command->getPayment();
        $this->modifyPayment($payment);

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        $details = $payment->getDetails();

        return in_array($details['resultCode'], self::ALLOWED_EVENT_NAMES);
    }
}
