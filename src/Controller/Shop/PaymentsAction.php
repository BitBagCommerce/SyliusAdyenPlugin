<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var OrderTokenAssignerInterface */
    private $orderTokenAssigner;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        UrlGeneratorInterface $urlGenerator,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        OrderTokenAssignerInterface $orderTokenAssigner,
        Dispatcher $dispatcher
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->urlGenerator = $urlGenerator;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->orderTokenAssigner = $orderTokenAssigner;
        $this->dispatcher = $dispatcher;
    }

    private function prepareTargetUrl(OrderInterface $order): string
    {
        $params = [
            'code'=>$order->getLastPayment()->getMethod()->getCode()
        ];

        return $this->urlGenerator->generate(
            self::REDIRECT_TARGET_ACTION,
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function prepareOrder(Request $request, OrderInterface $order)
    {
        if (!$request->get('tokenValue')) {
            $request->getSession()->set('sylius_order_id', $order->getId());
        }
        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();
        $this->prepareOrder($request, $order);

        $payment = $order->getLastPayment();
        $url = $this->prepareTargetUrl($order);

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->submitPayment(
            $order->getTotal(),
            $order->getCurrencyCode(),
            $payment->getId(),
            $url,
            $request->request->all()
        );

        $payment->setDetails($result);
        $this->dispatcher->dispatch(new PreparePayment($payment));

        return new JsonResponse($payment->getDetails() + ['redirect' => $url]);
    }
}
