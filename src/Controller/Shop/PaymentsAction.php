<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\GenericTokenFactoryInterface;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\Payment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    public const THANKS_PATH = 'sylius_shop_order_thank_you';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var Payum */
    private $payum;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var EntityManagerInterface */
    private $paymentManager;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;
    /**
     * @var OrderTokenAssignerInterface
     */
    private $orderTokenAssigner;
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        Payum $payum,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $paymentManager,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        OrderTokenAssignerInterface $orderTokenAssigner,
        FactoryInterface $stateMachineFactory
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->payum = $payum;
        $this->urlGenerator = $urlGenerator;
        $this->paymentManager = $paymentManager;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->orderTokenAssigner = $orderTokenAssigner;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    private function markOrderAsWaitingForPayment(OrderInterface $order, Request $request): void
    {
        $sm = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        if($sm->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)){
            $sm->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
        }

    }

    private function prepareTargetUrl(OrderInterface $order): string
    {
        return $this->urlGenerator->generate(
            self::THANKS_PATH,
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function __invoke(Request $request): JsonResponse
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $request->getSession()->set('sylius_order_id', $order->getId());
        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);

        $payload = $request->request->all();
        $payment = $order->getLastPayment();
        $url = $this->prepareTargetUrl($order);

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->submitPayment(
            $order->getTotal(),
            $order->getCurrencyCode(),
            $payment->getId(),
            $url,
            $payload
        );

        if($result['resultCode'] == 'Authorised'){
            $this->markOrderAsWaitingForPayment($order, $request);
            $result['redirect'] = $url;
        }

        $payment->setDetails($result);

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();

        return new JsonResponse($payment->getDetails());
    }
}
