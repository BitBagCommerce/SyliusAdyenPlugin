<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\EventSubscriber;

use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use SM\Event\SMEvents;
use SM\Event\TransitionEvent;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FilterManualRefundConfirmationSubscriber implements EventSubscriberInterface
{
    use GatewayConfigFromPaymentTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            SMEvents::TEST_TRANSITION => 'filter',
        ];
    }

    public function filter(TransitionEvent $event): void
    {
        if (
            RefundPaymentTransitions::GRAPH !== $event->getStateMachine()->getGraph()
            || RefundPaymentTransitions::TRANSITION_COMPLETE !== $event->getTransition()
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
