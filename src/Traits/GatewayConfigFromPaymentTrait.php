<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

trait GatewayConfigFromPaymentTrait
{
    private function getGatewayConfig(PaymentMethodInterface $paymentMethod): GatewayConfigInterface
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if ($gatewayConfig === null) {
            throw new \InvalidArgumentException(
                sprintf('PaymentMethod #%d has no GatewayConfig associated', $paymentMethod->getId())
            );
        }

        return $gatewayConfig;
    }
}
