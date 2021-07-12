<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Query;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class GetToken
{
    /** @var OrderInterface */
    private $order;

    /** @var PaymentMethodInterface */
    private $paymentMethod;

    public function __construct(PaymentMethodInterface $paymentMethod, OrderInterface $order)
    {
        $this->order = $order;
        $this->paymentMethod = $paymentMethod;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
