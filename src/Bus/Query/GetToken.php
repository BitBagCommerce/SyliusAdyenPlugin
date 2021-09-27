<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Query;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class GetToken
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
