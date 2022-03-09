<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Model;

final class StreetAddressModel implements StreetAddressModelInterface
{
    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $houseNumber;

    public function __construct(string $street, string $houseNumberOrName)
    {
        $this->street = $street;
        $this->houseNumber = $houseNumberOrName;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
}
