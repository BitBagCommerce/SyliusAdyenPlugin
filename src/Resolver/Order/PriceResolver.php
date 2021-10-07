<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;


use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

final class PriceResolver implements PriceResolverInterface
{
    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;
    /**
     * @var TaxRateResolverInterface
     */
    private $taxRateResolver;

    public function __construct(
        ZoneMatcherInterface $zoneMatcher,
        TaxRateResolverInterface $taxRateResolver
    )
    {
        ;
        $this->zoneMatcher = $zoneMatcher;
        $this->taxRateResolver = $taxRateResolver;
    }


    public function getNetPrice(OrderItemUnitInterface $orderItemUnit): int
    {
        $order = $orderItemUnit->getOrderItem()->getOrder();
        $item = $orderItemUnit->getOrderItem();

        $zone = $this->zoneMatcher->match($order->getBillingAddress());
        $taxRate = $this->taxRateResolver->resolve($orderItemUnit->getOrderItem()->getVariant(), ['zone' => $zone]);

        if ($taxRate === null) {
            return $item->getUnitPrice();
        }

        if ($taxRate->isIncludedInPrice()) {
            return $item->getUnitPrice();
        }

        return (int) round($item->getUnitPrice() + ($item->getTaxTotal() / $item->getQuantity()));
    }

    public function getPrice(OrderItemUnitInterface $orderItemUnit): int
    {
        $taxAdjustment = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        return 0.0;
    }
}