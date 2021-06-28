<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentDetailsAction
{
    use PayableOrderPaymentTrait;

    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        FactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator,
        Dispatcher $dispatcher
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
        $this->dispatcher = $dispatcher;
    }

    private function getTargetUrl(PaymentInterface $payment, ?string $tokenValue = null): string
    {
        return $this->urlGenerator->generate(
            self::REDIRECT_TARGET_ACTION,
            [
                'code'=>$payment->getMethod()->getCode(),
                'tokenValue'=>$tokenValue
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function __invoke(Request $request): Response
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();
        $payment = $this->getPayablePayment($order);

        $tokenValue = $request->query->get('tokenValue');
        if ($tokenValue === null) {
            $request->getSession()->set('sylius_order_id', $order->getId());
        }

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->paymentDetails($request->request->all());

        $payment->setDetails($result);
        $this->dispatcher->dispatch(new PreparePayment($payment));

        return new JsonResponse($payment->getDetails() + ['redirect'=>$this->getTargetUrl($payment, $tokenValue)]);
    }
}
