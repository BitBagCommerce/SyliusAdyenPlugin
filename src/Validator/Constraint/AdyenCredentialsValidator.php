<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Exception\InvalidApiKeyException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidMerchantAccountException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AdyenCredentialsValidator extends ConstraintValidator
{
    /** @var AdyenClientInterface */
    private $adyenClient;

    public function __construct(AdyenClientInterface $adyenClient)
    {
        $this->adyenClient = $adyenClient;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            $this->adyenClient->isApiKeyValid($value['environment'], $value['merchantAccount'], $value['apiKey']);
        } catch (InvalidApiKeyException $ex) {
            $this->context->buildViolation('invalid api key')->addViolation();
        } catch (InvalidMerchantAccountException $ex) {
            $this->context->buildViolation('invalid merchant account')->addViolation();
        }
    }
}
