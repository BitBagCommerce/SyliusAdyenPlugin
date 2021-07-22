<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\EventSubscriber;

use BitBag\SyliusAdyenPlugin\Bus\Command\RequestCapture;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Exception\UnprocessablePaymentException;
use BitBag\SyliusAdyenPlugin\Traits\OrderFromPaymentTrait;
use SM\Event\SMEvents;
use SM\Event\TransitionEvent;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoutePaymentCompleteTransitionSubscriber implements EventSubscriberInterface
{
    use OrderFromPaymentTrait;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        Dispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SMEvents::PRE_TRANSITION => 'doFilter',
            SMEvents::TEST_TRANSITION => 'canComplete'
        ];
    }

    private function isProcessableAdyenPayment(TransitionEvent $event): bool
    {
        if ($event->getStateMachine()->getGraph() !== PaymentTransitions::GRAPH) {
            return false;
        }

        if ($event->getTransition() !== PaymentTransitions::TRANSITION_COMPLETE) {
            return false;
        }
        if (!isset($this->getObject($event)->getDetails()['pspReference'])) {
            return false;
        }

        return true;
    }

    private function getObject(TransitionEvent $event): PaymentInterface
    {
        /**
         * @var ?PaymentInterface $object
         */
        $object = $event->getStateMachine()->getObject();
        if ($object === null) {
            throw new UnprocessablePaymentException();
        }

        return $object;
    }

    public function canComplete(TransitionEvent $event): void
    {
        if (
            !$this->isProcessableAdyenPayment($event)
            || $event->getState() !== PaymentInterface::STATE_PROCESSING
            || $event->getTransition() === 'capture'
        ) {
            return;
        }

        $event->setRejected();
    }

    public function doFilter(TransitionEvent $event): void
    {
        if (!$this->isProcessableAdyenPayment($event)) {
            return;
        }

        $this->dispatcher->dispatch(
            new RequestCapture(
                $this->getOrderFromPayment(
                    $this->getObject($event)
                )
            )
        );

        $event->setRejected();
        $event->getStateMachine()->apply(PaymentTransitions::TRANSITION_PROCESS, true);
    }
}
