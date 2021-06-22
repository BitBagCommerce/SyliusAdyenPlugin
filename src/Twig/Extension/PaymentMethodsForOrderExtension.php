<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Twig\Extension;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaymentMethodsForOrderExtension extends AbstractExtension
{
    public const CONFIGURATION_KEYS_WHITELIST = [
        'environment', 'merchantAccount', 'clientKey'
    ];

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentMethodRepository = $paymentMethodRepository;
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

    private function getPaymentMethod(OrderInterface $order, ?string $code = null): ?PaymentMethodInterface
    {
        if ($code) {
            return $this->paymentMethodRepository->findOneForAdyenAndCode($code);
        }

        return $order->getLastPayment()->getMethod();
    }

    public function adyenPaymentConfiguration(OrderInterface $order, ?string $code = null)
    {
        $paymentMethod = $this->getPaymentMethod($order, $code);

        if (!$paymentMethod) {
            return null;
        }

        $result = $this->filterKeys(
            $paymentMethod->getGatewayConfig()->getConfig()
        );
        $result['paymentMethods'] = $this->adyenPaymentMethods($order, $code);

        return $result;
    }

    private function adyenPaymentMethods(OrderInterface $order, ?string $code = null)
    {
        $method = $this->getPaymentMethod($order, $code);

        try {
            $client = $this->adyenClientProvider->getClientForCode($method->getCode());
        } catch (\InvalidArgumentException $ex) {
            return false;
        }

        return $client->getAvailablePaymentMethods(
            $order->getLocaleCode(),
            $order->getBillingAddress()->getCountryCode(),
            $order->getTotal(),
            $order->getCurrencyCode()
        );
    }
}
