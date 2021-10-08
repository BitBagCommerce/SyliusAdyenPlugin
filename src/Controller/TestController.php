<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Controller;

use BitBag\SyliusAdyenPlugin\Normalizer\OrderItemToLineItemNormalizer;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TestController
{
    private CartContextInterface $cartContext;

    private NormalizerInterface $normalizer;

    public function __construct(
        CartContextInterface $cartContext,
        NormalizerInterface $normalizer
    ) {
        $this->cartContext = $cartContext;
        $this->normalizer = $normalizer;
    }

    public function __invoke(Request $request)
    {
        $order = $this->cartContext->getCart();

        return new JsonResponse(
            $this->normalizer->normalize(
                $order->getItems()->first(),
                null,
                [OrderItemToLineItemNormalizer::NORMALIZER_ENABLED => true]
            )
        );
    }
}
