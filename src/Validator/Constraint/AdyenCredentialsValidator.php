<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Adyen\AdyenException;
use Adyen\Service\Checkout;
use BitBag\SyliusAdyenPlugin\Client\AdyenTransportFactory;
use BitBag\SyliusAdyenPlugin\Exception\AuthenticationException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidApiKeyException;
use BitBag\SyliusAdyenPlugin\Exception\InvalidMerchantAccountException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class AdyenCredentialsValidator extends ConstraintValidator
{
    /** @var AdyenTransportFactory */
    private $adyenTransportFactory;

    public function __construct(AdyenTransportFactory $adyenTransportFactory)
    {
        $this->adyenTransportFactory = $adyenTransportFactory;
    }

    private function dispatchException(AdyenException $exception): void
    {
        if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
            throw new InvalidApiKeyException();
        }

        if ($exception->getCode() === Response::HTTP_FORBIDDEN) {
            throw new InvalidMerchantAccountException();
        }

        throw $exception;
    }

    private function validateArguments(?string $merchantAccount, ?string $apiKey): void
    {
        if ($merchantAccount === null || $merchantAccount === '') {
            throw new InvalidMerchantAccountException();
        }
        if ($apiKey === null || $apiKey === '') {
            throw new InvalidApiKeyException();
        }
    }

    /**
     * @throws AuthenticationException|AdyenException
     */
    public function isApiKeyValid(string $environment, ?string $merchantAccount, ?string $apiKey): bool
    {
        $this->validateArguments($merchantAccount, $apiKey);

        $payload = [
            'merchantAccount' => $merchantAccount
        ];
        $options = [
            'environment' => $environment,
            'apiKey' => $apiKey
        ];

        try {
            (new Checkout(
                $this->adyenTransportFactory->create($options)
            ))->paymentMethods($payload);
        } catch (AdyenException $exception) {
            $this->dispatchException($exception);
        }

        return true;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, AdyenCredentials::class);
        Assert::isArray($value);

        try {
            $this->isApiKeyValid(
                (string) $value['environment'],
                (string) $value['merchantAccount'],
                (string) $value['apiKey']
            );
        } catch (InvalidApiKeyException $ex) {
            $this->context->buildViolation($constraint->messageInvalidApiKey)->addViolation();
        } catch (InvalidMerchantAccountException $ex) {
            $this->context->buildViolation($constraint->messageInvalidMerchantAccount)->addViolation();
        }
    }
}
