<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Adyen\Exception\HMACKeyValidationException;
use BitBag\SyliusAdyenPlugin\Provider\SignatureValidatorProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct\NotificationItemData;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class HmacSignatureValidator extends ConstraintValidator
{
    public const PAYMENT_METHOD_FIELD_NAME = 'paymentCode';

    /** @var SignatureValidatorProvider */
    private $signatureValidatorProvider;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(
        SignatureValidatorProvider $signatureValidatorProvider,
        PropertyAccessorInterface $propertyAccessor,
        NormalizerInterface $normalizer
    ) {
        $this->signatureValidatorProvider = $signatureValidatorProvider;
        $this->propertyAccessor = $propertyAccessor;
        $this->normalizer = $normalizer;
    }

    private function violate(bool $result, HmacSignature $constraint): void
    {
        if ($result) {
            return;
        }

        $this->context->buildViolation($constraint->message);
    }

    /**
     * @param object|array $value
     */
    private function getPaymentCode($value): string
    {
        try {
            return (string) $this->propertyAccessor->getValue(
                $value,
                self::PAYMENT_METHOD_FIELD_NAME
            );
        } catch (\InvalidArgumentException $exception) {
            /**
             * @var object|array $objectOrArray
             */
            $objectOrArray = $this->context->getRoot();

            return (string) $this->propertyAccessor->getValue(
                $objectOrArray,
                self::PAYMENT_METHOD_FIELD_NAME
            );
        }
    }

    private function getNormalizedNotificationData(NotificationItemData $value): array
    {
        $params = (array) $this->normalizer->normalize($value, 'xml');
        $params['success'] = $value->success ? 'true' : 'false';

        return $params;
    }

    /**
     * @param mixed $value
     * @param Constraint|HmacSignature $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, NotificationItemData::class);
        Assert::isInstanceOf($constraint, HmacSignature::class);

        $validator = $this->signatureValidatorProvider->getValidatorForCode(
            $this->getPaymentCode($value)
        );

        $params = $this->getNormalizedNotificationData($value);

        try {
            $result = $validator->isValid($params);
        } catch (HMACKeyValidationException $ex) {
            $result = false;
        }

        $this->violate($result, $constraint);
    }
}
