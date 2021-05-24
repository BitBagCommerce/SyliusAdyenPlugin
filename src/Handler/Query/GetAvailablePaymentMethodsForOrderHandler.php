<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Handler\Query;

use BitBag\SyliusAdyenPlugin\Adapters\Payment\PaymentAdapterInterface;
use BitBag\SyliusAdyenPlugin\Adapters\PaymentAdapterRegistryInterface;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Query\GetAvailablePaymentMethodsForOrder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GetAvailablePaymentMethodsForOrderHandler implements MessageHandlerInterface
{
    /** @var PaymentAdapterRegistryInterface<PaymentAdapterInterface> */
    private $paymentAdapters;

    /** @var AdyenClientInterface */
    private $adyenClient;

    public function __construct(
        AdyenClientInterface $adyenClient,
        PaymentAdapterRegistryInterface $paymentAdapters
    ) {
        $this->paymentAdapters = $paymentAdapters;
        $this->adyenClient = $adyenClient;
    }

    /**
     * @return PaymentAdapterInterface[]
     */
    private function hydratePaymentAdapters(array $paymentMethods): array
    {
        $result = [];
        foreach ($paymentMethods as $paymentMethod => $paymentMethodName) {
            if (!$this->paymentAdapters->has($paymentMethod)) {
                continue;
            }
            /**
             * @var $adapter PaymentAdapterInterface
             */
            $adapter = $this->paymentAdapters->get($paymentMethod);
            $adapter->setLocalizedName($paymentMethodName);
        }

        return $result;
    }

    /**
     * @return PaymentAdapterInterface[]
     */
    public function __invoke(GetAvailablePaymentMethodsForOrder $query): array
    {
        $order = $query->getOrder();
        $availablePaymentMethods = $this->adyenClient->getAvailablePaymentMethods(
            $order->getLocaleCode(),
            $order->getBillingAddress()->getCountryCode(),
            $order->getItemsTotal(),
            $order->getCurrencyCode()
        );

        return $this->hydratePaymentAdapters($availablePaymentMethods);
    }
}
