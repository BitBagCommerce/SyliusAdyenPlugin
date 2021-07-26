<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Callback;

use BitBag\SyliusAdyenPlugin\Bus\Command\CancelPayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;

class RequestCancelCallback
{
    /** @var Dispatcher */
    private $dispatcher;

    /** @var FactoryInterface */
    private $factory;

    public function __construct(
        FactoryInterface $factory,
        Dispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
        $this->factory = $factory;
    }

    public function __invoke(OrderInterface $order): void
    {
        $factory = $this->factory->get($order, OrderPaymentTransitions::GRAPH);

        if (!$factory->can(OrderPaymentTransitions::TRANSITION_CANCEL)) {
            return;
        }

        $this->dispatcher->dispatch(
            new CancelPayment($order)
        );
    }
}
