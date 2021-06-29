<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use BitBag\SyliusAdyenPlugin\Exception\AdyenNotConfigured;
use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

trait GatewayConfigFromPaymentTrait
{
    private function getGatewayConfig(PaymentMethodInterface $paymentMethod): GatewayConfigInterface
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if ($gatewayConfig === null) {
            throw new AdyenNotConfigured((string) $paymentMethod->getCode());
        }

        return $gatewayConfig;
    }
}
