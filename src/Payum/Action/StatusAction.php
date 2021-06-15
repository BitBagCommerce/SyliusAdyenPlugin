<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Payum\Action;

use BitBag\SyliusAdyenPlugin\Client\PaymentStatuses;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StatusAction implements ActionInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param GetStatusInterface|GetStatus $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        $details = $payment->getDetails();

        if (PaymentStatuses::PAYMENT_AUTHORISED === $details['resultCode']) {
            $this->markCompleted($payment);
            $request->markAuthorized();

            return;
        }
    }

    private function markCompleted(PaymentInterface $payment)
    {
        $payment->setState(PaymentInterface::STATE_COMPLETED);

        foreach ([
            OrderCheckoutTransitions::GRAPH => OrderCheckoutTransitions::TRANSITION_COMPLETE,
            OrderPaymentTransitions::GRAPH => OrderPaymentTransitions::TRANSITION_PAY
        ] as $graph=>$state) {
            $stateMachine = $this->stateMachineFactory->get($payment->getOrder(), $graph);
            $stateMachine->can($state) && $stateMachine->apply($state);
        }
    }

    /**
     * @param GetStatusInterface|GetStatus $request
     */
    public function supports($request): bool
    {
        return
            $request instanceof GetStatus &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}
