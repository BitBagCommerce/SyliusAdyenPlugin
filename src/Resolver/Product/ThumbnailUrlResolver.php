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
use Sylius\Component\Core\Model\ProductVariantInterface;

class ThumbnailUrlResolver implements ThumbnailUrlResolverInterface
{
    use FilterTrait;

    private const FILTER_NAME = 'sylius_shop_product_thumbnail';

    private function getProductImage(ProductVariantInterface $productVariant): ?ProductImageInterface
    {
        /**
         * @var ProductImageInterface[] $result
         */
        $result =
            $productVariant->getImagesByType('main')->toArray()
            + $productVariant->getProduct()->getImagesByType('main')->toArray()
        ;

        $image = array_shift($result);

        if ($image === false) {
            return null;
        }

        return $image;
    }

    public function resolve(ProductVariantInterface $productVariant): ?string
    {
        $image = $this->getProductImage($productVariant);
        if ($image === null) {
            return null;
        }

        return $this->filter($image->getPath(), self::FILTER_NAME);
    }
}
