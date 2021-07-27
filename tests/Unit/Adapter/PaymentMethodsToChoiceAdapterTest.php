<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Adapter;

use BitBag\SyliusAdyenPlugin\Adapter\PaymentMethodsToChoiceAdapter;
use PHPUnit\Framework\TestCase;

class PaymentMethodsToChoiceAdapterTest extends TestCase
{
    /** @var PaymentMethodsToChoiceAdapter */
    private $adapter;

    protected function setUp(): void
    {
        $this->adapter = new PaymentMethodsToChoiceAdapter();
    }

    public function testInvalidPayload(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ($this->adapter)([]);
    }

    public static function provideForTestAdaptation(): array
    {
        return [
            'without cards' => [
                [
                    'paymentMethods' => [
                        [
                            'type' => 'chrzaszcz',
                            'name' => 'Szczebrzeszyn'
                        ]
                    ]
                ],
                [
                    'chrzaszcz' => 'Szczebrzeszyn'
                ]
            ],
            'with cards' => [
                [
                    'paymentMethods' => [
                        [
                            'type' => 'grzegorz',
                            'name' => 'Brzęczyszczykiewicz'
                        ],
                        [
                            'type' => 'scheme',
                            'name' => 'Credit card',
                            'brands' => [
                                'visa', 'mc'
                            ]
                        ]
                    ]
                ],
                [
                    'grzegorz' => 'Brzęczyszczykiewicz',
                    'visa' => 'Credit card',
                    'mc' => 'Credit card'
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideForTestAdaptation
     */
    public function testAdaptation(array $payload, array $expected): void
    {
        $result = ($this->adapter)($payload);
        $this->assertEquals($expected, $result);
    }
}
