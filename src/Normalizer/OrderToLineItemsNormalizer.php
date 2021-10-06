<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;


use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Webmozart\Assert\Assert;

class OrderToLineItemsNormalizer implements ContextAwareNormalizerInterface
{
    public const NORMALIZER_ENABLED = 'order_to_line_items_normalizer';


    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(!isset($context[self::NORMALIZER_ENABLED])){
            return false;
        }

        return $data instanceof OrderInterface;
    }

    /**
     *
    amountExcludingTax
    Item amount excluding the tax, in minor units.

    amountIncludingTax
    Item amount including the tax, in minor units.

    description
    Description of the line item.

    id
    ID of the line item.

    imageUrl
    Link to the picture of the purchased item.

    itemCategory
    Item category, used by the RatePay payment method.

    productUrl
    Link to the purchased item.

    quantity
    Number of items.

    taxAmount
    Tax amount, in minor units.

    taxPercentage
    Tax percentage, in minor units.
     */

    /**
     * @param OrderInterface $object
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $result = [];

        foreach($object->getItems() as $item){
            $result[] = [
                'amountIncludingTax' => $item->getTotal(),
                'amountExcludingTax' =>
            ]
        }
    }
}