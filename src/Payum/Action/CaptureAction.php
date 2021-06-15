<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\Capture;
use Sylius\Component\Core\Model\PaymentInterface;

class CaptureAction implements ActionInterface
{
    public function execute($request)
    {
        // TODO: Implement execute() method.
    }

    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
