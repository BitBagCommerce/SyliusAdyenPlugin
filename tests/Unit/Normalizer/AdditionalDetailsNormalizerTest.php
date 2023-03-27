<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Normalizer;

use BitBag\SyliusAdyenPlugin\Normalizer\AbstractPaymentNormalizer;
use BitBag\SyliusAdyenPlugin\Normalizer\AdditionalDetailsNormalizer;
use BitBag\SyliusAdyenPlugin\Normalizer\ShippingLineGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tests\BitBag\SyliusAdyenPlugin\Unit\AddressMother;
use Tests\BitBag\SyliusAdyenPlugin\Unit\Mock\RequestMother;
use Tests\BitBag\SyliusAdyenPlugin\Unit\OrderMother;

class AdditionalDetailsNormalizerTest extends TestCase
{
    private const EXPECTED_DELEGATED_NORMALIZER_RESULT = ['Bakłażan', 'ze', 'Szczebrzeszyna'];
    private const EXPECTED_SHIPPING_LINE = ['do', 'chrząszcza'];

    /** @var \BitBag\SyliusAdyenPlugin\Normalizer\AdditionalDetailsNormalizer|object|null */
    private $normalizer;
    /** @var \PHPUnit\Framework\MockObject\MockObject|NormalizerInterface */
    private $delegatedNormalizer;
    /** @var RequestStack */
    private $requestStack;
    /** @var ShippingLineGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $shippingLineGenerator;

    protected function setUp(): void
    {
        $this->shippingLineGenerator = $this->createMock(ShippingLineGeneratorInterface::class);

        $this->requestStack = new RequestStack();
        $this->normalizer = new AdditionalDetailsNormalizer($this->requestStack, $this->shippingLineGenerator);
        $this->delegatedNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($this->delegatedNormalizer);
    }

    public static function provideForSupportsNormalization(): array
    {
        return [
            'without context and order' => [[], null, false],
            'without context' => [[], new Order(), false],
            'with context and order' => [[AbstractPaymentNormalizer::NORMALIZER_ENABLED => 1], new Order(), true],
        ];
    }

    /**
     * @dataProvider provideForSupportsNormalization
     */
    public function testSupportsNormalization(
        array $context,
        ?OrderInterface $order,
        bool $pass
    ): void
    {
        $this->assertEquals($pass, $this->normalizer->supportsNormalization($order, null, $context));
    }

    public function testNormalize(): void
    {
        $this->delegatedNormalizer
            ->method('normalize')
            ->willReturn(self::EXPECTED_DELEGATED_NORMALIZER_RESULT)
        ;

        $this->setupShippingLine();
        $this->setupRequest();

        $target = OrderMother::createForNormalization();
        $result = $this->normalizer->normalize($target);

        $this->assertEquals([
            'billingAddress' => self::EXPECTED_DELEGATED_NORMALIZER_RESULT,
            'deliveryAddress' => self::EXPECTED_DELEGATED_NORMALIZER_RESULT,
            'lineItems' => [
                self::EXPECTED_DELEGATED_NORMALIZER_RESULT,
                self::EXPECTED_DELEGATED_NORMALIZER_RESULT,
                self::EXPECTED_SHIPPING_LINE,
            ],
            'shopperEmail' => OrderMother::CUSTOMER_EMAIL,
            'shopperName' => [
                'firstName' => OrderMother::CUSTOMER_FIRST_NAME,
                'lastName' => OrderMother::CUSTOMER_LAST_NAME,
            ],
            'shopperIP' => RequestMother::WHERE_YOUR_HOME_IS,
            'telephoneNumber' => AddressMother::BILLING_PHONE_NUMBER,
        ], $result);
    }

    private function setupShippingLine(): void
    {
        $this->shippingLineGenerator
            ->method('generate')
            ->willReturn(self::EXPECTED_SHIPPING_LINE)
        ;
    }

    private function setupRequest(): void
    {
        $this->requestStack->push(RequestMother::createWithLocaleSet());
    }
}
