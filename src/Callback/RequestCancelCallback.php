<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Callback;

use BitBag\SyliusAdyenPlugin\Bus\Command\CancelPayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use Sylius\Component\Core\Model\OrderInterface;

class RequestCancelCallback
{
    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        Dispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(OrderInterface $order): void
    {
        $this->dispatcher->dispatch(
            new CancelPayment($order)
        );
    }
}
