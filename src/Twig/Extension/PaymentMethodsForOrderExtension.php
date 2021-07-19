<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Twig\Extension;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaymentMethodsForOrderExtension extends AbstractExtension
{
    use PaymentFromOrderTrait;
    use GatewayConfigFromPaymentTrait;

    public const CONFIGURATION_KEYS_WHITELIST = [
        'environment', 'merchantAccount', 'clientKey'
    ];

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        Dispatcher $dispatcher
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->dispatcher = $dispatcher;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('adyen_payment_configuration', [$this, 'adyenPaymentConfiguration'])
        ];
    }

    private function filterKeys(array $array): array
    {
        return array_intersect_key($array, array_flip(self::CONFIGURATION_KEYS_WHITELIST));
    }

    private function getPaymentMethod(OrderInterface $order, ?string $code = null): PaymentMethodInterface
    {
        if ($code !== null) {
            return $this->paymentMethodRepository->getOneForAdyenAndCode($code);
        }

        return $this->getMethod($this->getPayment($order));
    }

    public function adyenPaymentConfiguration(OrderInterface $order, ?string $code = null): ?array
    {
        $paymentMethod = $this->getPaymentMethod($order, $code);
        $token = $this->getToken($paymentMethod, $order);

        if (!isset($this->getGatewayConfig($paymentMethod)->getConfig()[AdyenClientProvider::FACTORY_NAME])) {
            return null;
        }

        $result = $this->filterKeys(
            $this->getGatewayConfig($paymentMethod)->getConfig()
        );
        $result['paymentMethods'] = $this->adyenPaymentMethods($order, $code, $token);
        $result['code'] = $paymentMethod->getCode();

        return $result;
    }

    private function getToken(PaymentMethodInterface $paymentMethod, OrderInterface $order): ?AdyenTokenInterface
    {
        /**
         * @var ?CustomerInterface $customer
         */
        $customer = $order->getCustomer();
        if ($customer === null || !$customer->hasUser()) {
            return null;
        }

        /**
         * @var AdyenTokenInterface $token
         */
        $token = $this->dispatcher->dispatch(new GetToken($paymentMethod, $order));

        return $token;
    }

    private function getCountryCode(OrderInterface $order): string
    {
        $address = $order->getBillingAddress();
        if ($address === null) {
            return '';
        }

        return (string) $address->getCountryCode();
    }

    private function adyenPaymentMethods(
        OrderInterface $order,
        ?string $code = null,
        ?AdyenTokenInterface $adyenToken = null
    ): array {
        $method = $this->getPaymentMethod($order, $code);

        try {
            $client = $this->adyenClientProvider->getClientForCode((string) $method->getCode());
        } catch (\InvalidArgumentException $ex) {
            return [];
        }

        return $client->getAvailablePaymentMethods(
            (string) $order->getLocaleCode(),
            $this->getCountryCode($order),
            $order->getTotal(),
            (string) $order->getCurrencyCode(),
            $adyenToken
        );
    }
}
