<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Normalizer;

use BitBag\SyliusAdyenPlugin\Normalizer\ShippingLineGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShippingLineGeneratorTest extends TestCase
{
    public const ITEM_NAME = 'Pacanów';

    /** @var \PHPUnit\Framework\MockObject\MockObject|TranslatorInterface */
    private $translator;
    /** @var ShippingLineGenerator */
    private $generator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->generator = new ShippingLineGenerator($this->translator);
    }

    public function testGenerating(): void
    {
        $this->setupTranslator();

        $entries = [
            ['amountExcludingTax' => 24, 'amountIncludingTax' => 30],
            ['amountExcludingTax' => 35, 'amountIncludingTax' => 42],
        ];

        $order = OrderMother::createForNormalization();

        $result = $this->generator->generate($entries, $order);
        $this->assertEquals([
            'amountExcludingTax' => 25,
            'amountIncludingTax' => 32,
            'id' => self::ITEM_NAME,
            'quantity' => 1,
        ], $result);
    }

    private function setupTranslator(): void
    {
        $this->translator
            ->method('trans')
            ->willReturn(self::ITEM_NAME)
        ;
    }
}
