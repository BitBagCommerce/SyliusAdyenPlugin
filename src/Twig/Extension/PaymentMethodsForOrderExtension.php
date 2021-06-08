<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Twig\Extension;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaymentMethodsForOrderExtension extends AbstractExtension
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(AdyenClientProvider $adyenClientProvider)
    {
        $this->adyenClientProvider = $adyenClientProvider;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('adyen_payment_methods', [$this, 'adyenPaymentMethods']),
            new TwigFunction('adyen_payment_configuration', [$this, 'adyenPaymentConfiguration'])
        ];
    }

    public function adyenPaymentConfiguration(OrderInterface $order)
    {
        /**
         * @var $payment PaymentMethodInterface
         */
        $payment = $order->getLastPayment();

        return $payment->getMethod()->getGatewayConfig()->getConfig();
    }

    public function adyenPaymentMethods(OrderInterface $order)
    {
        /**
         * @var $payment PaymentMethodInterface
         */
        $payment = $order->getLastPayment();

        try {
            $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
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