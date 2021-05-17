<?php


namespace BitBag\SyliusAdyenPlugin\Adapters;


use BitBag\SyliusAdyenPlugin\Adapters\Payment\CardAdapter;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PaymentAdapterRegistry extends ServiceLocator implements PaymentAdapterRegistryInterface
{
    const PAYMENT_METHODS_MAPPING = [
        'scheme' => CardAdapter::class
    ];

    public function __construct()
    {
        parent::__construct(self::PAYMENT_METHODS_MAPPING);
    }


}