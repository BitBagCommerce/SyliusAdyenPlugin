<?php


namespace BitBag\SyliusAdyenPlugin\Action;


use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

class StatusAction implements ActionInterface
{

    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        $details = $payment->getDetails();

        if (200 === $details['status']) {
            $request->markCaptured();

            return;
        }

        if (400 === $details['status']) {
            $request->markFailed();

            return;
        }
    }

    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }
}