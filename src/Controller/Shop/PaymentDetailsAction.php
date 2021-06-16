<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentDetailsAction
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var Payum */
    private $payum;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        Payum $payum
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->payum = $payum;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
    }

    private function triggerPayumAction(PaymentInterface $payment)
    {
        $request = new GetStatus($payment);
        $this->payum->getGateway($payment->getMethod()->getCode())->execute($request);

        if ($request->isAuthorized()) {
            return;
        }
        $details = $payment->getDetails();
        unset($details['redirect']);
        $payment->setDetails($details);
    }

    public function __invoke(Request $request)
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $payment = $order->getLastPayment();

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->paymentDetails($request->request->all());
        $payment->setDetails($result);

        $this->triggerPayumAction($payment);

        return new JsonResponse($payment->getDetails());
    }
}
