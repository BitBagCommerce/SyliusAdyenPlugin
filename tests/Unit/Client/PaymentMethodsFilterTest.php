<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Client;

use BitBag\SyliusAdyenPlugin\Client\PaymentMethodsFilter;
use BitBag\SyliusAdyenPlugin\Client\PaymentMethodsFilterInterface;
use PHPUnit\Framework\TestCase;

class PaymentMethodsFilterTest extends TestCase
{
    private function getFilter(?array $supportedMethodsList): PaymentMethodsFilterInterface
    {
        return new PaymentMethodsFilter($supportedMethodsList);
    }

    public static function provideForTestFilter(): array
    {
        return [
            'empty supported methods list' => [
                [
                    ['type' => 'first'],
                    ['type' => 'second'],
                ],
                null,
                [
                    ['type' => 'first'],
                    ['type' => 'second'],
                ],
            ],
            'non-empty supported list' => [
                [
                    ['type' => 'first'],
                    ['type' => 'second'],
                    ['type' => 'third'],
                ],
                ['first', 'third'],
                [
                    ['type' => 'first'],
                    ['type' => 'third'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideForTestFilter
     */
    public function testFilter(
        array $paymentMethodsResponseList,
        ?array $supportedMethodsList,
        array $expected
    ): void {
        $response = [
            'paymentMethods' => $paymentMethodsResponseList,
        ];

        $filter = $this->getFilter($supportedMethodsList);
        $result = $filter->filter($response);

        $expected = [
            'paymentMethods' => $expected,
        ];

        $this->assertEquals($expected, $result);
    }
}
