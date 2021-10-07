<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Controller;


use BitBag\SyliusAdyenPlugin\Normalizer\OrderToLineItemsNormalizer;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TestController
{
    /**
     * @var CartContextInterface
     */
    private CartContextInterface $cartContext;
    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $normalizer;

    public function __construct(
        CartContextInterface $cartContext,
        NormalizerInterface $normalizer
    )
    {
        $this->cartContext = $cartContext;
        $this->normalizer = $normalizer;
    }


    public function __invoke(Request $request)
    {
        $order = $this->cartContext->getCart();

        return new JsonResponse(
            $this->normalizer->normalize(
                $order,
                null,
                [OrderToLineItemsNormalizer::NORMALIZER_ENABLED=>true]
            )
        );
    }

}