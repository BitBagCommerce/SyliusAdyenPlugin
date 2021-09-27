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
use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentDetailsAction
{
    use PayableOrderPaymentTrait;
    use PaymentFromOrderTrait;

    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var DispatcherInterface */
    private $dispatcher;

    public function __construct(
        AdyenClientProviderInterface $adyenClientProvider,
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        UrlGeneratorInterface $urlGenerator,
        DispatcherInterface $dispatcher
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->urlGenerator = $urlGenerator;
        $this->dispatcher = $dispatcher;
    }

    private function getTargetUrl(PaymentInterface $payment, ?string $tokenValue = null): string
    {
        $method = $this->getMethod($payment);

        return $this->urlGenerator->generate(
            self::REDIRECT_TARGET_ACTION,
            [
                'code' => $method->getCode(),
                'tokenValue' => $tokenValue,
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

        $client = $this->adyenClientProvider->getForPaymentMethod(
            $this->getMethod($payment)
        );
        $result = $client->paymentDetails($request->request->all());

        $payment->setDetails($result);
        $this->dispatcher->dispatch(new PaymentStatusReceived($payment));

        return new JsonResponse(
            $payment->getDetails()
            + [
                'redirect' => $this->getTargetUrl($payment, $tokenValue === null ? null : (string) $tokenValue),
            ]
        );
    }
}
