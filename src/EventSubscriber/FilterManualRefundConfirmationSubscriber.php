<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\EventSubscriber;

use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use SM\Event\SMEvents;
use SM\Event\TransitionEvent;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterManualRefundConfirmationSubscriber implements EventSubscriberInterface
{
    use GatewayConfigFromPaymentTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            SMEvents::TEST_TRANSITION => 'filter'
        ];
    }

    public function filter(TransitionEvent $event): void
    {
        if (
            $event->getStateMachine()->getGraph() !== RefundPaymentTransitions::GRAPH
            || $event->getTransition() !== RefundPaymentTransitions::TRANSITION_COMPLETE
        ) {
            return;
        }

        /**
         * @var RefundPaymentInterface $object
         */
        $object = $event->getStateMachine()->getObject();
        if (!isset($this->getGatewayConfig($object->getPaymentMethod())->getConfig()['adyen'])) {
            return;
        }

        $event->setRejected();
    }
}
