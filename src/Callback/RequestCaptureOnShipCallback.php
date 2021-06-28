<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Callback;

use BitBag\SyliusAdyenPlugin\Bus\Command\RequestCapture;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use Sylius\Component\Core\Model\OrderInterface;

class RequestCaptureOnShipCallback
{
    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(OrderInterface $order): void
    {
        $this->dispatcher->dispatch(
            new RequestCapture($order)
        );
    }
}
