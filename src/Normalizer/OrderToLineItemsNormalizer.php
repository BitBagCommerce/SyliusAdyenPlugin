<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;


use BitBag\SyliusAdyenPlugin\Resolver\Order\PriceResolverInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Product\ThumbnailUrlResolver;
use BitBag\SyliusAdyenPlugin\Resolver\Product\ThumbnailUrlResolverInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class OrderToLineItemsNormalizer implements ContextAwareNormalizerInterface
{
    public const NORMALIZER_ENABLED = 'order_to_line_items_normalizer';
    const DEFAULT_DESCRIPTION_LOCALE = 'en';
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var ThumbnailUrlResolverInterface
     */
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
        if(!isset($context[self::NORMALIZER_ENABLED])){
            return false;
        }

        return $data instanceof OrderInterface;
    }

    /**
     * @param OrderInterface $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $result = [];

        $locale =
            $this->requestStack->getMainRequest()
            ? $this->requestStack->getMainRequest()->getLocale()
            : self::DEFAULT_DESCRIPTION_LOCALE
        ;

        /**
         * @var OrderItemInterface $item
         */
        foreach($object->getItems() as $item){

            $amountWithoutTax = $item->getTotal() - $item->getTaxTotal();
            $product = $item->getVariant()->getProduct();

            $result[] = [
                'description' => $item->getVariant()->getTranslation($locale)->getName(),
                'amountIncludingTax' => $item->getTotal(),
                'amountExcludingTax' => $amountWithoutTax,
                'taxAmount' => $item->getTaxTotal(),
                'taxPercentage' => (int)round((($item->getTotal()/$amountWithoutTax)-1)*100),
                'quantity' => $item->getQuantity(),
                'id' => $item->getId(),
                'productUrl' => $this->urlGenerator->generate('sylius_shop_product_show', [
                    'slug'=>$product->getSlug()
                ]),
                'imageUrl' => $this->thumbnailUrlResolver->resolve($item->getVariant())
            ];
        }

        return $result;
    }
}