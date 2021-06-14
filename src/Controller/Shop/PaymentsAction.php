<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\AdyenActionResolver;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var AdyenActionResolver */
    private $adyenActionResolver;
    /**
     * @var Payum
     */
    private $payum;
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $paymentManager;


    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $messageBus,
        AdyenActionResolver $adyenActionResolver,
        Payum $payum,
        FactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $paymentManager
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->orderRepository = $orderRepository;
        $this->messageBus = $messageBus;
        $this->adyenActionResolver = $adyenActionResolver;
        $this->payum = $payum;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
        $this->paymentManager = $paymentManager;
    }

    public function __invoke(int $orderId, Request $request)
    {
        /**
         * @var $order OrderInterface
         */
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException();
        }

        $payload = $request->request->all();
        if(!$payload){
            throw new \InvalidArgumentException();
        }

        $payment = $order->getLastPayment();

        //$action = $this->adyenActionResolver->resolve($order, $result['resultCode'], $result);
        $sm = $this->stateMachineFactory->get($order, 'sylius_order');
        $sm->can('create') && $sm->apply('create');

        $url = $this->urlGenerator->generate(
            'sylius_shop_order_pay',
            ['tokenValue'=>$order->getTokenValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->submitPayment(
            $order->getTotal(),
            $order->getCurrencyCode(),
            $order->getTokenValue(),
            $url,
            $payload
        );
        $payment->setDetails($result);

        if(!empty($result['resultCode']) && $result['resultCode'] == 'Authorised'){
            $sm = $this->stateMachineFactory->get($order, 'sylius_order_checkout');
            $sm->can('complete') && $sm->apply('complete');
        }

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();

        $result['redirect'] = $url;
        return new JsonResponse($result);
    }
}
