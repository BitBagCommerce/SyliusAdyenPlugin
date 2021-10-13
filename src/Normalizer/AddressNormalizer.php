<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;

use BitBag\SyliusAdyenPlugin\Client\ClientPayloadFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

final class AddressNormalizer extends AbstractPaymentNormalizer
{
    /**
     * @param mixed|AddressInterface $data
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return parent::supportsNormalization($data, $format, $context) && $data instanceof AddressInterface;
    }

    /**
     * @param mixed $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, AddressInterface::class);

        return [
            'street' => (string) $object->getStreet(),
            'postalCode' => (string) $object->getPostcode(),
            'city' => (string) $object->getCity(),
            'country' => $object->getCountryCode() ?? ClientPayloadFactoryInterface::NO_COUNTRY_AVAILABLE_PLACEHOLDER,
            'stateOrProvince' => (string) $object->getProvinceName(),
            'houseNumberOrName' => '',
        ];
    }
}
