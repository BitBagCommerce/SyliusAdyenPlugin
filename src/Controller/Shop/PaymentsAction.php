<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Payment\Model\Payment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    public const ORDER_PAY_PATH = 'sylius_shop_order_pay';

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

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        Payum $payum,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $paymentManager,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        OrderTokenAssignerInterface $orderTokenAssigner
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->payum = $payum;
        $this->urlGenerator = $urlGenerator;
        $this->paymentManager = $paymentManager;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->orderTokenAssigner = $orderTokenAssigner;
    }

    private function prepareOrder(OrderInterface $order, Request $request): void
    {
        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);
        $request->getSession()->set('sylius_order_id', $order->getId());
    }

    /**
     * @param PaymentInterface|Payment $payment
     */
    private function triggerPayumAction(PaymentInterface $payment, string $url): bool
    {
        $status = new GetStatus($payment);
        $this->payum->getGateway($payment->getMethod()->getCode())->execute($status);

        $details = $payment->getDetails();

        if (!$status->isAuthorized()) {
            $details['paymentDetailsUrl'] = $this->urlGenerator->generate(
                'bitbag_adyen_payment_details',
                ['tokenValue'=>$payment->getOrder()->getTokenValue()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $payment->setDetails($details);

            return false;
        }

        $details['redirect'] = $url;
        $payment->setDetails($details);

        return true;
    }

    private function prepareTargetUrl(OrderInterface $order): string
    {
        return $this->urlGenerator->generate(
            self::ORDER_PAY_PATH,
            ['tokenValue'=>$order->getTokenValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function __invoke(Request $request): JsonResponse
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $payload = $request->request->all();
        $payment = $order->getLastPayment();
        $this->prepareOrder($order, $request);
        $url = $this->prepareTargetUrl($order);

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->submitPayment(
            $order->getTotal(),
            $order->getCurrencyCode(),
            $order->getTokenValue(),
            $url,
            $payload
        );
        $payment->setDetails($result);

        $this->triggerPayumAction($payment, $url);

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();

        return new JsonResponse($payment->getDetails());
    }
}
