<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Address;

use BitBag\SyliusAdyenPlugin\Model\StreetAddressModel;
use BitBag\SyliusAdyenPlugin\Model\StreetAddressModelInterface;

/**
 * Ported from:
 *
 * @see https://github.com/Adyen/adyen-magento2/blob/master/Helper/Address.php
 */
final class StreetAddressResolver implements StreetAddressResolverInterface
{
    // Regex to extract the house number from the street line if needed (e.g. 'Street address 1 A' => '1 A')
    private const STREET_FIRST_REGEX = "/(?<streetName>[[:alnum:].'\- ]+)\s+(?<houseNumber>\d{1,10}((\s)?\w{1,3})?(\/\d{1,10})?)$/";

    private const NUMBER_FIRST_REGEX = "/^(?<houseNumber>\d{1,10}((\s)?\w{1,3})?(\/\d{1,10})?)\s+(?<streetName>[[:alnum:].'\- ]+)/u";

    public function resolve(string $streetAddress): StreetAddressModelInterface
    {
        // Match addresses where the street name comes first, e.g. John-Paul's Ave. 1 B
        \preg_match(self::STREET_FIRST_REGEX, \trim($streetAddress), $streetFirstAddress);

        // Match addresses where the house number comes first, e.g. 10 D John-Paul's Ave.
        \preg_match(self::NUMBER_FIRST_REGEX, \trim($streetAddress), $numberFirstAddress);

        if (0 < \count($streetFirstAddress)) {
            return $this->getAddress($streetFirstAddress['streetName'] ?? '', $streetFirstAddress['houseNumber'] ?? '');
        }
        if (0 < \count($numberFirstAddress)) {
            return $this->getAddress($numberFirstAddress['streetName'] ?? '', $numberFirstAddress['houseNumber'] ?? '');
        }

        return $this->getAddress($streetAddress, 'N/A');
    }

    private function getAddress(string $street, string $houseNumber): StreetAddressModelInterface
    {
        return new StreetAddressModel(
            $street,
            $houseNumber
        );
    }
}
