<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Controller;


use BitBag\SyliusAdyenPlugin\Resolver\Order\PriceResolverInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController
{
    /**
     * @var CartContextInterface
     */
    private CartContextInterface $cartContext;
    /**
     * @var PriceResolverInterface
     */
    private PriceResolverInterface $priceResolver;

    public function __construct(
        CartContextInterface $cartContext,
        PriceResolverInterface $priceResolver
    )
    {
        $this->cartContext = $cartContext;
        $this->priceResolver = $priceResolver;
    }


    public function __invoke(Request $request)
    {
        $order = $this->cartContext->getCart();

        $units = $order->getItems()->toArray();
        /**
         * @var OrderItemInterface $item
         */
        $item = end($units);
        $orderItemUnit = $item->getUnits()->current();

        $net = $this->priceResolver->getNetPrice($orderItemUnit);

        return new JsonResponse($net);
    }

}