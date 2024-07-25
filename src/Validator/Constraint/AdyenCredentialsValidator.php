<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
        if (Response::HTTP_UNAUTHORIZED === $exception->getCode()) {
            throw new InvalidApiKeyException();
        }

        if (Response::HTTP_FORBIDDEN === $exception->getCode()) {
            throw new InvalidMerchantAccountException();
        }

        throw $exception;
    }

    private function validateArguments(?string $merchantAccount, ?string $apiKey): void
    {
        if (null === $merchantAccount || '' === $merchantAccount) {
            throw new InvalidMerchantAccountException();
        }

        if (null === $apiKey || '' === $apiKey) {
            throw new InvalidApiKeyException();
        }
    }

    /**
     * @throws AuthenticationException|AdyenException
     */
    public function isApiKeyValid(
        string $environment,
        ?string $merchantAccount,
        ?string $apiKey,
        ?string $liveEndpointUrlPrefix,
    ): bool {
        $this->validateArguments($merchantAccount, $apiKey);

        $payload = [
            'merchantAccount' => $merchantAccount,
        ];
        $options = [
            'environment' => $environment,
            'apiKey' => $apiKey,
            'liveEndpointUrlPrefix' => $liveEndpointUrlPrefix,
        ];

        try {
            (new Checkout(
                $this->adyenTransportFactory->create($options),
            ))->paymentMethods($payload);
        } catch (AdyenException $exception) {
            $this->dispatchException($exception);
        }

        return true;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, AdyenCredentials::class);
        Assert::isArray($value);

        try {
            $this->isApiKeyValid(
                (string) $value['environment'],
                (string) $value['merchantAccount'],
                (string) $value['apiKey'],
                (string) $value['liveEndpointUrlPrefix'],
            );
        } catch (InvalidApiKeyException $ex) {
            $this->context->buildViolation($constraint->messageInvalidApiKey)->addViolation();
        } catch (InvalidMerchantAccountException $ex) {
            $this->context->buildViolation($constraint->messageInvalidMerchantAccount)->addViolation();
        }
    }
}
