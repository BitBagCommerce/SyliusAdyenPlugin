<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\AdyenActionResolver;
use Payum\Core\Security\GenericTokenFactory;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentsAction
{
    /**
     * @var AdyenClientProvider
     */
    private $adyenClientProvider;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;
    /**
     * @var AdyenActionResolver
     */
    private $adyenActionResolver;
    /**
     * @var GenericTokenFactory
     */
    private $genericTokenFactory;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $messageBus,
        AdyenActionResolver $adyenActionResolver,
        GenericTokenFactory $genericTokenFactory
    )
    {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->orderRepository = $orderRepository;
        $this->messageBus = $messageBus;
        $this->adyenActionResolver = $adyenActionResolver;
        $this->genericTokenFactory = $genericTokenFactory;
    }


    public function __invoke(int $orderId, Request $request)
    {
        /**
         * @var $order OrderInterface
         */
        $order = $this->orderRepository->find($orderId);
        if(!$order){
            throw new NotFoundHttpException();
        }

        $payment = $order->getLastPayment();

        /*$token = $this->genericTokenFactory->createToken(
            $payment->getMethod()->getCode(), $order,
        )*/

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $payments = $client->submitPayment(
            $order->getTotal(), $order->getCurrencyCode()
        );

        // TODO: Implement __invoke() method.
    }
}
