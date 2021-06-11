<?php


namespace BitBag\SyliusAdyenPlugin\Action;


use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\PaymentInterface;

class ConvertPaymentAction implements \Payum\Core\Action\ActionInterface
{

    /**
     * @inheritDoc
     */
    public function execute($request)
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function supports($request)
    {
        return
            $request instanceof Convert
            && $request->getSource() instanceof PaymentInterface
        ;
    }
}