<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Callback\PreserveOrderTokenUponRedirectionCallback;
use BitBag\SyliusAdyenPlugin\Provider\PaymentMethodsForOrderProvider;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class DropinConfigurationAction
{
    /** @var CartContextInterface */
    private $cartContext;
    /** @var PaymentMethodsForOrderProvider */
    private $paymentMethodsForOrderExtension;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        CartContextInterface $cartContext,
        PaymentMethodsForOrderProvider $paymentMethodsForOrderExtension,
        UrlGeneratorInterface $urlGenerator,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->cartContext = $cartContext;
        $this->paymentMethodsForOrderExtension = $paymentMethodsForOrderExtension;
        $this->urlGenerator = $urlGenerator;
        $this->orderRepository = $orderRepository;
    }

    private function getResponseForDroppedOrder(Request $request): JsonResponse
    {
        /**
         * @var ?string $tokenValue
         */
        $tokenValue = $request->getSession()->get(
            PreserveOrderTokenUponRedirectionCallback::NON_FINALIZED_CART_SESSION_KEY
        );

        if ($tokenValue === null) {
            throw new NotFoundHttpException();
        }

        $response = new JsonResponse([
            'redirect' => $this->urlGenerator->generate('sylius_shop_order_show', [
                'tokenValue' => $tokenValue,
            ]),
        ]);
        $request->getSession()->remove(PreserveOrderTokenUponRedirectionCallback::NON_FINALIZED_CART_SESSION_KEY);

        return $response;
    }

    public function __invoke(Request $request, string $code, ?string $orderToken = null): JsonResponse
    {
        /**
         * @var ?OrderInterface $order
         */
        if ($orderToken !== null) {
            $order = $this->orderRepository->findOneByTokenValue($orderToken);
        } else {
            $order = $this->cartContext->getCart();
        }

        if ($order === null || $order->getId() === null) {
            return $this->getResponseForDroppedOrder($request);
        }

        Assert::isInstanceOf($order, OrderInterface::class);

        $config = $this->paymentMethodsForOrderExtension->provideConfiguration($order, $code);
        Assert::isArray($config);

        $billingAddress = $order->getBillingAddress();
        Assert::isInstanceOf($billingAddress, AddressInterface::class);

        $pathParams = [
            'code' => $code,
            'tokenValue' => $order->getTokenValue(),
        ];

        return new JsonResponse([
            'billingAddress' => [
                'firstName' => $billingAddress->getFirstName(),
                'lastName' => $billingAddress->getLastName(),
                'countryCode' => $billingAddress->getCountryCode(),
                'province' => $billingAddress->getProvinceName() ?? $billingAddress->getProvinceCode(),
                'city' => $billingAddress->getCity(),
                'postcode' => $billingAddress->getPostcode(),
            ],
            'paymentMethods' => $config['paymentMethods'],
            'clientKey' => $config['clientKey'],
            'locale' => $order->getLocaleCode(),
            'environment' => $config['environment'],
            'canBeStored' => $config['canBeStored'],
            'amount' => [
                'currency' => $order->getCurrencyCode(),
                'value' => $order->getTotal(),
            ],
            'path' => [
                'payments' => $this->urlGenerator->generate('bitbag_adyen_payments', $pathParams),
                'paymentDetails' => $this->urlGenerator->generate('bitbag_adyen_payment_details', $pathParams),
                'deleteToken' => $this->urlGenerator->generate(
                    'bitbag_adyen_remove_token',
                    $pathParams + ['paymentReference' => '_REFERENCE_']
                ),
            ],
        ]);
    }
}
