<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client\ClientPayloadFactory;


use BitBag\SyliusAdyenPlugin\Client\ClientPayloadFactoryInterface;
use BitBag\SyliusAdyenPlugin\Exception\UnboundAddressFromOrderException;
use Sylius\Component\Core\Model\OrderInterface;

/*
 * Billing address

Shipping address

Shopper email

Shopper name

Line items

Shopper IP

Telephone number

Browser info
 */
final class OrderDataAssembler implements OrderDataAssemblerInterface
{
    private function createFraudDetectionData(OrderInterface $order): array
    {
        $billingAddress = $order->getBillingAddress();

        if ($billingAddress === null) {
            throw new UnboundAddressFromOrderException($order);
        }

        return [
            'street' => (string) $billingAddress->getStreet(),
            'postalCode' => (string) $billingAddress->getPostcode(),
            'city' => (string) $billingAddress->getCity(),
            'country' => $billingAddress->getCountryCode() ?? ClientPayloadFactoryInterface::NO_COUNTRY_AVAILABLE_PLACEHOLDER,
            'stateOrProvince' => (string) $billingAddress->getProvinceName(),
        ];
    }

    public function assemble(OrderInterface $order): array
    {
        return [];
    }
}