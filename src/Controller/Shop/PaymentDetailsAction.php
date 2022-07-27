<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessorInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentDetailsAction
{
    use PayableOrderPaymentTrait;

    use PaymentFromOrderTrait;

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var PaymentResponseProcessorInterface */
    private $paymentResponseProcessor;

    public function __construct(
        AdyenClientProviderInterface $adyenClientProvider,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        PaymentResponseProcessorInterface $paymentResponseProcessor
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->paymentResponseProcessor = $paymentResponseProcessor;
    }

    public function __invoke(Request $request): Response
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();
        $payment = $this->getPayablePayment($order);
        $paymentMethod = $this->getMethod($payment);

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
        $result = $client->paymentDetails($request->request->all());

        $payment->setDetails($result);

        return new JsonResponse(
            $payment->getDetails()
            + [
                'redirect' => $this->paymentResponseProcessor->process(
                    (string) $paymentMethod->getCode(),
                    $request,
                    $payment
                ),
            ]
        );
    }
}
