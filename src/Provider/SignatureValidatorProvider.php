<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\SignatureValidator;
use BitBag\SyliusAdyenPlugin\Exception\AdyenNotConfiguredException;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;

class SignatureValidatorProvider
{
    use GatewayConfigFromPaymentTrait;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getValidatorForCode(string $code): SignatureValidator
    {
        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($code);

        if ($paymentMethod === null) {
            throw new AdyenNotConfiguredException($code);
        }
        $gatewayConfig = $this->getGatewayConfig($paymentMethod);

        return new SignatureValidator(
            (string) $gatewayConfig->getConfig()['hmacKey']
        );
    }
}
