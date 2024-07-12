<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Product;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ThumbnailUrlResolver implements ThumbnailUrlResolverInterface
{
    private const FILTER_NAME = 'sylius_shop_product_thumbnail';

    private const IMAGE_TYPE = 'main';

    /** @var CacheManager */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    private function getProductImagesForVariant(ProductVariantInterface $productVariant): array
    {
        /**
         * @var ProductInterface|null $product
         */
        $product = $productVariant->getProduct();
        if (null === $product) {
            return [];
        }

        return $product->getImagesByType(self::IMAGE_TYPE)->toArray();
    }

    private function getProductImage(ProductVariantInterface $productVariant): ?ProductImageInterface
    {
        /**
         * @var ProductImageInterface[] $result
         */
        $result =
            $productVariant->getImagesByType(self::IMAGE_TYPE)->toArray()
            +
            $this->getProductImagesForVariant($productVariant)
        ;

        return array_shift($result);
    }

    public function resolve(ProductVariantInterface $productVariant): ?string
    {
        $image = $this->getProductImage($productVariant);
        if (null === $image) {
            return null;
        }

        $path = $image->getPath();
        Assert::notNull($path);

        return $this->cacheManager->generateUrl($path, self::FILTER_NAME);
    }
}
