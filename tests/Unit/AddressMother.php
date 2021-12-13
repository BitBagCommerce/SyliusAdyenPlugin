<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit;

use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;

final class AddressMother
{
    public const BILLING_CITY = 'Szczebrzeszyn';
    public const BILLING_POSTCODE = '22-460';
    public const BILLING_STREET = 'Zamojska 1';
    public const BILLING_PROVINCE = 'lubelskie';
    public const BILLING_PHONE_NUMBER = '+4242424242';

    public const SHIPPING_CITY = 'Wrocław';
    public const SHIPPING_POSTCODE = '54-530';
    public const SHIPPING_STREET = 'Ćwiartki 3/1';
    public const SHIPPING_PROVINCE = 'dolnośląskie';

    public const ADDRESS_COUNTRY = 'PL';

    public static function createBillingAddress(): AddressInterface
    {
        $billingAddress = new Address();
        $billingAddress->setCity(self::BILLING_CITY);
        $billingAddress->setPostcode(self::BILLING_POSTCODE);
        $billingAddress->setStreet(self::BILLING_STREET);
        $billingAddress->setCountryCode(self::ADDRESS_COUNTRY);
        $billingAddress->setProvinceName(self::BILLING_PROVINCE);
        $billingAddress->setPhoneNumber(self::BILLING_PHONE_NUMBER);

        return $billingAddress;
    }

    public static function createShippingAddress(): AddressInterface
    {
        $shippingAddress = new Address();
        $shippingAddress->setCity(self::SHIPPING_CITY);
        $shippingAddress->setPostcode(self::SHIPPING_POSTCODE);
        $shippingAddress->setStreet(self::SHIPPING_STREET);
        $shippingAddress->setCountryCode(self::ADDRESS_COUNTRY);
        $shippingAddress->setProvinceName(self::SHIPPING_PROVINCE);

        return $shippingAddress;
    }

    public static function createAddressWithSpecifiedCountryAndEmptyProvince(
        string $country = self::ADDRESS_COUNTRY
    ): AddressInterface {
        $result = self::createShippingAddress();
        $result->setCountryCode($country);
        $result->setProvinceCode(null);
        $result->setProvinceName(null);

        return $result;
    }
}
