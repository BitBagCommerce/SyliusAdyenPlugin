<?php


namespace BitBag\SyliusAdyenPlugin\Query;


use Sylius\Component\Core\Model\OrderInterface;

class GetAvailablePaymentMethodsForOrder
{
    private $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        return $this->order;
    }



}