<?php


namespace BitBag\SyliusAdyenPlugin\Action;


use Payum\Core\Action\ActionInterface;
use Payum\Core\Model\Identity;
use Payum\Core\Request\Capture;
use Sylius\Component\Core\Model\PaymentInterface;

class CaptureAction implements ActionInterface
{

    public function execute($request)
    {
        return;
    }

    public function supports($request)
    {
        return
            $request instanceof Capture
            && $request->getModel() instanceof Identity
            && is_subclass_of($request->getModel()->getClass(), PaymentInterface::class)
        ;
    }
}