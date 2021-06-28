<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Command\PreparePayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    use PayableOrderPaymentTrait;

    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    public const NO_COUNTRY_AVAILABLE_PLACEHOLDER = 'ZZ';

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

    private function prepareOrder(Request $request, OrderInterface $order): void
    {
        if ($request->get('tokenValue') === null) {
            $request->getSession()->set('sylius_order_id', $order->getId());
        }
        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);
    }

    private function createFraudDetectionData(OrderInterface $order): array
    {
        $billingAddress = $order->getBillingAddress();

        return [
            'street' => $billingAddress->getStreet(),
            'postalCode' => $billingAddress->getPostcode(),
            'city' => $billingAddress->getCity(),
            'country' => $billingAddress->getCountryCode() ?? self::NO_COUNTRY_AVAILABLE_PLACEHOLDER,
            'stateOrProvince' => $billingAddress->getProvinceName()
        ];
    }

    private function createPaymentPayload(Request $request, OrderInterface $order): array
    {
        $result = $request->request->all();
        if (isset($result['paymentMethod']['brand'])) {
            $result['paymentMethod']['billingAddress'] = $this->createFraudDetectionData($order);
        }

        return $result;
    }

    public function __invoke(Request $request, ?string $code = null): JsonResponse
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();
        $this->prepareOrder($request, $order);

        if ($code !== null) {
            $this->dispatcher->dispatch(new TakeOverPayment($order, $code));
        }

        $payment = $this->getPayablePayment($order);
        $url = $this->prepareTargetUrl($order);

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $result = $client->submitPayment(
            $order->getTotal(),
            $order->getCurrencyCode(),
            $payment->getId(),
            $url,
            $this->createPaymentPayload($request, $order)
        );

        $payment->setDetails($result);
        $this->dispatcher->dispatch(new PreparePayment($payment));

        return new JsonResponse($payment->getDetails() + ['redirect' => $url]);
    }
}
