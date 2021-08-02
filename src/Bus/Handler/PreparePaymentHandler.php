<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
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

    /** @var EntityRepository */
    private $paymentRepository;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityRepository $paymentRepository
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentRepository = $paymentRepository;
    }

    public function __invoke(PreparePayment $command): void
    {
        $payment = $command->getPayment();
        if (!$this->isAccepted($payment)) {
            return;
        }

        $this->updateOrderState($this->getOrderFromPayment($payment));
        $this->paymentRepository->add($payment);
    }

    private function updateOrderState(OrderInterface $order): void
    {
        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE, true);
    }

    private function isAccepted(PaymentInterface $payment): bool
    {
        $details = $payment->getDetails();

        return in_array($details['resultCode'], self::ALLOWED_EVENT_NAMES, true);
    }
}
