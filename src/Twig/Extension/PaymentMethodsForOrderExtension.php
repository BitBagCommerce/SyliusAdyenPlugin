<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Twig\Extension;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
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

        if (!isset($this->getGatewayConfig($paymentMethod)->getConfig()['adyen'])) {
            return null;
        }

        $result = $this->filterKeys(
            $this->getGatewayConfig($paymentMethod)->getConfig()
        );
        $result['paymentMethods'] = $this->adyenPaymentMethods($order, $code);

        return $result;
    }

    private function getCountryCode(OrderInterface $order): string
    {
        $address = $order->getBillingAddress();
        if ($address === null) {
            return '';
        }

        return (string) $address->getCountryCode();
    }

    private function adyenPaymentMethods(OrderInterface $order, ?string $code = null): array
    {
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
            (string) $order->getCurrencyCode()
        );
    }
}
