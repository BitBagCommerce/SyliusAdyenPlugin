<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\SignatureValidator;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;

class SignatureValidatorProvider
{
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

        if (!$paymentMethod) {
            throw new \InvalidArgumentException(sprintf('Adyen for "%s" code is not configured', $code));
        }

        return new SignatureValidator(
            $paymentMethod->getGatewayConfig()->getConfig()['hmacKey']
        );
    }
}
