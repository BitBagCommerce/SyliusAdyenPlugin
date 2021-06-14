<?php


namespace BitBag\SyliusAdyenPlugin\Action;


use BitBag\SyliusAdyenPlugin\Client\PaymentStatuses;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;

class StatusAction implements ActionInterface
{

    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();

        $details = $payment->getDetails();

        if (PaymentStatuses::PAYMENT_AUTHORISED === $details['resultCode']) {
            $payment->setState(PaymentInterface::STATE_COMPLETED);

            return;
        }

        if (400 === $details['resultCode']) {
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