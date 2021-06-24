<?php


namespace BitBag\SyliusAdyenPlugin\Bus\Command;


use Sylius\Component\Core\Model\OrderInterface;

class TakeOverPayment
{
    /**
     * @var OrderInterface
     */
    private $order;
    /**
     * @var string
     */
    private $paymentCode;

    public function __construct(OrderInterface $order, string $paymentCode)
    {
        $this->order = $order;
        $this->paymentCode = $paymentCode;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getPaymentCode(): string
    {
        return $this->paymentCode;
    }



}