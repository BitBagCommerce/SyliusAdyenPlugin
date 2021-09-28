<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\AdyenClient;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface AdyenClientProviderInterface
{
    public const FACTORY_NAME = 'adyen';

    public function getDefaultClient(): AdyenClient;

    public function getClientForCode(string $code): AdyenClientInterface;

    public function getForPaymentMethod(PaymentMethodInterface $paymentMethod): AdyenClientInterface;
}
