<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Command\PaymentStatusReceived;
use BitBag\SyliusAdyenPlugin\Bus\Command\PrepareOrderForPayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessorInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentsAction
{
    use PayableOrderPaymentTrait;
    use PaymentFromOrderTrait;

    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    public const ORDER_ID_KEY = 'sylius_order_id';

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var PaymentResponseProcessorInterface */
    private $paymentResponseProcessor;

    public function __construct(
        AdyenClientProviderInterface $adyenClientProvider,
        UrlGeneratorInterface $urlGenerator,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        DispatcherInterface $dispatcher,
        PaymentResponseProcessorInterface $paymentResponseProcessor
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->urlGenerator = $urlGenerator;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->dispatcher = $dispatcher;
        $this->paymentResponseProcessor = $paymentResponseProcessor;
    }

    private function prepareTargetUrl(OrderInterface $order): string
    {
        $method = $this->getMethod(
            $this->getPayment($order)
        );

        return $this->urlGenerator->generate(
            self::REDIRECT_TARGET_ACTION,
            [
                'code' => $method->getCode(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function prepareOrder(Request $request, OrderInterface $order): void
    {
        if ($request->get('tokenValue') === null) {
            $request->getSession()->set(self::ORDER_ID_KEY, $order->getId());
        }

        $this->dispatcher->dispatch(new PrepareOrderForPayment($order));
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
        $paymentMethod = $this->getMethod($payment);
        /**
         * @var AdyenTokenInterface $customerIdentifier
         */
        $customerIdentifier = $this->dispatcher->dispatch(new GetToken($paymentMethod, $order));

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
        $result = $client->submitPayment(
            $url,
            $request->request->all(),
            $order,
            $customerIdentifier
        );

        $payment->setDetails($result);
        $this->dispatcher->dispatch(new PaymentStatusReceived($payment));

        return new JsonResponse(
            $payment->getDetails()
            +
            [
                'redirect' => $this->paymentResponseProcessor->process(
                    (string) $paymentMethod->getCode(),
                    $request,
                    $payment
                ),
            ]
        );
    }
}
