<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Exception\InvalidApiKeyException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidMerchantAccountException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class AdyenCredentialsValidator extends ConstraintValidator
{
    /** @var AdyenClientInterface */
    private $adyenClient;

    public function __construct(AdyenClientInterface $adyenClient)
    {
        $this->adyenClient = $adyenClient;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, AdyenCredentials::class);

        if (!isset($value['merchantAccount'][0])) {
            return;
        }

        try {
            $this->adyenClient->isApiKeyValid($value['environment'], $value['merchantAccount'], $value['apiKey']);
        } catch (InvalidApiKeyException $ex) {
            $this->context->buildViolation($constraint->messageInvalidApiKey)->addViolation();
        } catch (InvalidMerchantAccountException $ex) {
            $this->context->buildViolation($constraint->messageInvalidMerchantAccount)->addViolation();
        }
    }
}
