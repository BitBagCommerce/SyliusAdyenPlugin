<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;

use BitBag\SyliusAdyenPlugin\Resolver\Product\ThumbnailUrlResolverInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Webmozart\Assert\Assert;

class OrderItemToLineItemNormalizer implements ContextAwareNormalizerInterface
{
    public const NORMALIZER_ENABLED = 'order_item_to_line_item_normalizer';

    private const DEFAULT_DESCRIPTION_LOCALE = 'en';

    /** @var RequestStack */
    private $requestStack;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ThumbnailUrlResolverInterface */
    private $thumbnailUrlResolver;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        ThumbnailUrlResolverInterface $thumbnailUrlResolver
    )
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->thumbnailUrlResolver = $thumbnailUrlResolver;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (!isset($context[self::NORMALIZER_ENABLED])) {
            return false;
        }

        return $data instanceof OrderItemInterface;
    }

    /**
     * @param OrderItemInterface $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {

        $locale =
            $this->requestStack->getMainRequest()
                ? $this->requestStack->getMainRequest()->getLocale()
                : self::DEFAULT_DESCRIPTION_LOCALE
        ;

        $amountWithoutTax = $object->getTotal() - $object->getTaxTotal();
        $productVariant = $object->getVariant();

        Assert::notNull($productVariant);
        $product = $productVariant->getProduct();

        return [
            'description' => $productVariant->getTranslation($locale)->getName(),
            'amountIncludingTax' => $object->getTotal(),
            'amountExcludingTax' => $amountWithoutTax,
            'taxAmount' => $object->getTaxTotal(),
            'taxPercentage' => (int)round((($object->getTotal() / $amountWithoutTax) - 1) * 100),
            'quantity' => $object->getQuantity(),
            'id' => $object->getId(),
            'productUrl' => $this->urlGenerator->generate('sylius_shop_product_show', [
                'slug' => $product->getSlug(),
            ]),
            'imageUrl' => $this->thumbnailUrlResolver->resolve($productVariant),
        ];
    }
}
