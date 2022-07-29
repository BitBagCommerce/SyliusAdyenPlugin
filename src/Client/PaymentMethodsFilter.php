<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Webmozart\Assert\Assert;

final class PaymentMethodsFilter implements PaymentMethodsFilterInterface
{
    /** @var array|null */
    private $supportedMethodsList;

    public function __construct(?array $supportedMethodsList)
    {
        $this->supportedMethodsList = $supportedMethodsList;
    }

    private function doFilter(array $methodsList): array
    {
        $result = array_filter($methodsList, function (array $item): bool {
            Assert::keyExists($item, 'type');

            return in_array($item['type'], (array) $this->supportedMethodsList, true);
        }, \ARRAY_FILTER_USE_BOTH);

        return array_values($result);
    }

    public function filter(array $paymentMethodsResponse): array
    {
        Assert::keyExists($paymentMethodsResponse, 'paymentMethods');

        if (0 < count((array) $this->supportedMethodsList)) {
            $paymentMethodsResponse['paymentMethods'] = $this->doFilter(
                (array) $paymentMethodsResponse['paymentMethods']
            );
        }

        return $paymentMethodsResponse;
    }
}
