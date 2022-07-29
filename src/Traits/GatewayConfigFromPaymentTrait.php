<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Traits;

use BitBag\SyliusAdyenPlugin\Exception\AdyenNotConfiguredException;
use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

trait GatewayConfigFromPaymentTrait
{
    private function getGatewayConfig(PaymentMethodInterface $paymentMethod): GatewayConfigInterface
    {
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            throw new AdyenNotConfiguredException((string) $paymentMethod->getCode());
        }

        return $gatewayConfig;
    }
}
