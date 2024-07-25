<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PaymentMethodsForOrderProvider implements PaymentMethodsForOrderProviderInterface
{
    use PaymentFromOrderTrait;
    use GatewayConfigFromPaymentTrait;

    public const CONFIGURATION_KEYS_WHITELIST = [
        'environment', 'merchantAccount', 'clientKey',
    ];

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var DispatcherInterface */
    private $dispatcher;

    public function __construct(
        AdyenClientProviderInterface $adyenClientProvider,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        DispatcherInterface $dispatcher,
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->dispatcher = $dispatcher;
    }

    public function provideConfiguration(OrderInterface $order, ?string $code = null): ?array
    {
        $paymentMethod = $this->getPaymentMethod($order, $code);
        $token = $this->getToken($paymentMethod, $order);

        if (!isset($this->getGatewayConfig($paymentMethod)->getConfig()[AdyenClientProviderInterface::FACTORY_NAME])) {
            return null;
        }

        $result = $this->filterKeys(
            $this->getGatewayConfig($paymentMethod)->getConfig(),
        );
        $result['paymentMethods'] = $this->adyenPaymentMethods($order, $code, $token);
        $result['code'] = $paymentMethod->getCode();
        $result['canBeStored'] = null !== $token;

        return $result;
    }

    private function getToken(PaymentMethodInterface $paymentMethod, OrderInterface $order): ?AdyenTokenInterface
    {
        /**
         * @var ?CustomerInterface $customer
         */
        $customer = $order->getCustomer();
        if (null === $customer || !$customer->hasUser()) {
            return null;
        }

        /**
         * @var AdyenTokenInterface $token
         */
        $token = $this->dispatcher->dispatch(new GetToken($paymentMethod, $order));

        return $token;
    }

    private function adyenPaymentMethods(
        OrderInterface $order,
        ?string $code = null,
        ?AdyenTokenInterface $adyenToken = null,
    ): array {
        $method = $this->getPaymentMethod($order, $code);

        try {
            $client = $this->adyenClientProvider->getClientForCode((string) $method->getCode());
        } catch (\InvalidArgumentException $ex) {
            return [];
        }

        return $client->getAvailablePaymentMethods(
            $order,
            $adyenToken,
        );
    }

    private function filterKeys(array $array): array
    {
        return array_intersect_key($array, array_flip(self::CONFIGURATION_KEYS_WHITELIST));
    }

    private function getPaymentMethod(OrderInterface $order, ?string $code = null): PaymentMethodInterface
    {
        if (null !== $code) {
            return $this->paymentMethodRepository->getOneForAdyenAndCode($code);
        }

        return $this->getMethod($this->getPayment($order));
    }
}
