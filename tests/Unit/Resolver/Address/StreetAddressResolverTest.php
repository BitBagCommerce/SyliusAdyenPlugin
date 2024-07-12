<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Resolver\Address;

use BitBag\SyliusAdyenPlugin\Resolver\Address\StreetAddressResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Address\StreetAddressResolverInterface;
use PHPUnit\Framework\TestCase;

final class StreetAddressResolverTest extends TestCase
{
    private const UNKNOWN_HOUSE_NUMBER_OR_NAME = 'N/A';

    private const EXAMPLE_STREET_ADDRESS = 'Paleisstraat';

    /** @var StreetAddressResolverInterface */
    private $streetAddressResolver;

    protected function setUp(): void
    {
        $this->streetAddressResolver = new StreetAddressResolver();
    }

    /** @dataProvider provideHouseNumberFirst */
    public function testResolveHouseNumberFirst(
        string $streetAddress,
        string $street,
        string $houseNumber,
    ): void {
        $model = $this->streetAddressResolver->resolve($streetAddress);

        self::assertEquals($street, $model->getStreet());
        self::assertEquals($houseNumber, $model->getHouseNumber());
    }

    /** @dataProvider provideHouseNumberLast */
    public function testResolveHouseNumberLast(
        string $streetAddress,
        string $street,
        string $houseNumber,
    ): void {
        $model = $this->streetAddressResolver->resolve($streetAddress);

        self::assertEquals($street, $model->getStreet());
        self::assertEquals($houseNumber, $model->getHouseNumber());
    }

    public function testEmptyStreetAddress(): void
    {
        $model = $this->streetAddressResolver->resolve('');

        self::assertEquals('', $model->getStreet());
        self::assertEquals(self::UNKNOWN_HOUSE_NUMBER_OR_NAME, $model->getHouseNumber());
    }

    public function testEmptyHouseNumberOrName(): void
    {
        $model = $this->streetAddressResolver->resolve(self::EXAMPLE_STREET_ADDRESS);

        self::assertEquals(self::EXAMPLE_STREET_ADDRESS, $model->getStreet());
        self::assertEquals(self::UNKNOWN_HOUSE_NUMBER_OR_NAME, $model->getHouseNumber());
    }

    public function provideHouseNumberLast(): array
    {
        return [
            ['Zamojska 1', 'Zamojska', '1'],
            ['Morska 28d', 'Morska', '28d'],
            ['ul. Parkowa 1d', 'ul. Parkowa', '1d'],
            ['Akacjowa 98 z', 'Akacjowa', '98 z'],
            ['ul. Akacjowa 76 b', 'ul. Akacjowa', '76 b'],
            ['Krakowska 1/2', 'Krakowska', '1/2'],
            ['Krakowska 1d/2', 'Krakowska', '1d/2'],
        ];
    }

    public function provideHouseNumberFirst(): array
    {
        return [
            ['1 Montfortanenlaan', 'Montfortanenlaan', '1'],
            ['2D Gasthuislaan', 'Gasthuislaan', '2D'],
            ['98 W Molstraat', 'Molstraat', '98 W'],
            ['76B/2 ul. Akacjowa', 'ul. Akacjowa', '76B/2'],
        ];
    }
}
