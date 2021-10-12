<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Product;

use Liip\ImagineBundle\Templating\FilterTrait;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

class ThumbnailUrlResolver implements ThumbnailUrlResolverInterface
{
    use FilterTrait;

    private const FILTER_NAME = 'sylius_shop_product_thumbnail';
    private const IMAGE_TYPE = 'main';

    private function getProductImagesForVariant(ProductVariantInterface $productVariant): array
    {
        /**
         * @var ProductInterface|null $product
         */
        $product = $productVariant->getProduct();
        if ($product === null) {
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
        if ($image === null) {
            return null;
        }

        $path = $image->getPath();
        Assert::notNull($path);

        return $this->filter($path, self::FILTER_NAME);
    }
}
