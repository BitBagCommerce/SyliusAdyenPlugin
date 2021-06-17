<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentDetailsAction
{
    public const THANKS_PATH = 'sylius_shop_order_thank_you';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var Payum */
    private $payum;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var EntityManagerInterface */
    private $paymentManager;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        Payum $payum,
        FactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $paymentManager
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->payum = $payum;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
        $this->paymentManager = $paymentManager;
    }

    private function markOrderAsWaitingForPayment(OrderInterface $order, Request $request): void
    {
        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        if ($sm->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
        }
    }

    public function __invoke(Request $request)
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $payment = $order->getLastPayment();

        $request->getSession()->set('sylius_order_id', $order->getId());

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->paymentDetails($request->request->all());

        if ($result['resultCode'] == 'Authorised') {
            $this->markOrderAsWaitingForPayment($order, $request);
            $result['redirect'] = $this->urlGenerator->generate(
                self::THANKS_PATH,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $payment->setDetails($result);

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();

        return new JsonResponse($payment->getDetails());
    }
}
