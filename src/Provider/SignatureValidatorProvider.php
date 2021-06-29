<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\SignatureValidator;
use BitBag\SyliusAdyenPlugin\Exception\AdyenNotConfigured;
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
            throw new AdyenNotConfigured($code);
        }
        $gatewayConfig = $this->getGatewayConfig($paymentMethod);

        return new SignatureValidator(
            (string) $gatewayConfig->getConfig()['hmacKey']
        );
    }
}
