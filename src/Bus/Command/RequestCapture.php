<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use Sylius\Component\Core\Model\PaymentInterface;

class RequestCapture
{
    /** @var PaymentInterface */
    private $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }
}
