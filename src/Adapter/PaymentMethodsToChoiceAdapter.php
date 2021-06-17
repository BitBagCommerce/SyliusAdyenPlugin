<?php


namespace BitBag\SyliusAdyenPlugin\Adapter;


class PaymentMethodsToChoiceAdapter
{
    public function __invoke(array $paymentMethods)
    {
        if(empty($paymentMethods['paymentMethods'])){
            throw new \InvalidArgumentException(sprintf('Invalid Adyen paymentMethods response'));
        }

        $result = [];
        foreach ($paymentMethods['paymentMethods'] as $paymentMethod) {
            if (!empty($paymentMethod['brands'])) {
                foreach ($paymentMethod['brands'] as $brand) {
                    $result[$brand] = $paymentMethod['name'];
                }

                continue;
            }
            $result[$paymentMethod['type']] = $paymentMethod['name'];
        }

        return $result;
    }

}