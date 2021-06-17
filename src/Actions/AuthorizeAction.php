<?php


namespace BitBag\SyliusAdyenPlugin\Actions;


use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;

class AuthorizeAction implements AdyenAction
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;
    /**
     * @var EntityManagerInterface
     */
    private $orderManager;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        EntityManagerInterface $orderManager
    )
    {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
    }


    public function __invoke(PaymentInterface $payment, array $notificationData = [])
    {
        $payment->setState(PaymentInterface::STATE_COMPLETED);
        $order = $payment->getOrder();

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        if($stateMachine->can(OrderPaymentTransitions::TRANSITION_PAY)){
            $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);
        }

        $this->orderManager->persist($order);
        $this->orderManager->flush();
    }

    public function accept(PaymentInterface $payment): bool
    {
        return $payment->getOrder()->getPaymentState() !== OrderPaymentStates::STATE_PAID;
    }

    public static function getHandledEventName(): string
    {
        return 'AUTHORISATION';
    }
}