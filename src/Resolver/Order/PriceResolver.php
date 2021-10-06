<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;


use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class PriceResolver implements PriceResolverInterface
{
    /**
     * @var ChannelContextInterface
     */
    private ChannelContextInterface $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }


    public function getNetPrice(OrderItemUnitInterface $orderItemUnit): float
    {
        $this->channelContext->getChannel()
        //

        return 0.0;
    }

    public function getPrice(OrderItemUnitInterface $orderItemUnit): float
    {
        $taxAdjustment = $orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        return 0.0;
    }
}