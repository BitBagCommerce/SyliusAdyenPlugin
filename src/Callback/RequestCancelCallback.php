<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Callback;

use BitBag\SyliusAdyenPlugin\Bus\Command\CancelPayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;

final class RequestCancelCallback
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
