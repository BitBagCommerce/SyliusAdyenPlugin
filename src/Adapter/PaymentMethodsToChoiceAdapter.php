<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Adapter;

class PaymentMethodsToChoiceAdapter
{
    public function __invoke(array $paymentMethods): array
    {
        if (!isset($paymentMethods['paymentMethods'])) {
            throw new \InvalidArgumentException(sprintf('Invalid Adyen paymentMethods response'));
        }

        $result = [];
        /**
         * @var array $paymentMethod
         */
        foreach ($paymentMethods['paymentMethods'] as $paymentMethod) {
            $subResult = $this->adjustCardPaymentMethodResult($paymentMethod);

            if (count($subResult) > 0) {
                $result = array_merge($result, $subResult);

                continue;
            }

            $result[(string) $paymentMethod['type']] = (string) $paymentMethod['name'];
        }

        return $result;
    }

    private function adjustCardPaymentMethodResult(array $payload): array
    {
        if (!isset($payload['brands']) || !is_array($payload['brands'])) {
            return [];
        }

        $result = [];

        /**
         * @var string $brand
         */
        foreach ($payload['brands'] as $brand) {
            if (!isset($payload['name'])) {
                continue;
            }

            $result[$brand] = (string) $payload['name'];
        }

        return $result;
    }
}
