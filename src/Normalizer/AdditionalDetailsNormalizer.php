<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Normalizer;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class AdditionalDetailsNormalizer extends AbstractPaymentNormalizer implements NormalizerAwareInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var ?NormalizerInterface */
    private $normalizer;

    /** @var ShippingLineGeneratorInterface */
    private $shippingLineGenerator;

    public function __construct(
        RequestStack $requestStack,
        ShippingLineGeneratorInterface $shippingLineGenerator
    ) {
        $this->requestStack = $requestStack;
        $this->shippingLineGenerator = $shippingLineGenerator;
    }

    /**
     * @param mixed|OrderInterface $data
     */
    public function supportsNormalization(
        $data,
        string $format = null,
        array $context = []
    ): bool {
        return parent::supportsNormalization($data, $format, $context) && $data instanceof OrderInterface;
    }

    /**
     * @param object $object
     */
    private function normalizeInternalStructure($object): array
    {
        Assert::notNull($this->normalizer);

        return (array) $this->normalizer->normalize(
            $object,
            null,
            [AbstractPaymentNormalizer::NORMALIZER_ENABLED => true]
        );
    }

    private function getLineItems(OrderInterface $order): array
    {
        $result = [];

        foreach ($order->getItems() as $item) {
            $result[] = $this->normalizeInternalStructure($item);
        }

        return $result;
    }

    /**
     * @param mixed $object
     */
    public function normalize(
        $object,
        string $format = null,
        array $context = []
    ): array {
        Assert::isInstanceOf($object, OrderInterface::class);

        $customer = $object->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $request = $this->requestStack->getCurrentRequest();
        Assert::isInstanceOf($request, Request::class);

        $billingAddress = $object->getBillingAddress();
        Assert::isInstanceOf($billingAddress, AddressInterface::class);

        $shippingAddress = $object->getShippingAddress();
        Assert::isInstanceOf($shippingAddress, AddressInterface::class);

        $lineItems = $this->getLineItems($object);
        $lineItems[] = $this->shippingLineGenerator->generate($lineItems, $object);

        return [
            'billingAddress' => $this->normalizeInternalStructure($billingAddress),
            'deliveryAddress' => $this->normalizeInternalStructure($shippingAddress),
            'lineItems' => $lineItems,
            'shopperEmail' => (string) $customer->getEmail(),
            'shopperName' => [
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
            ],
            'shopperIP' => $request->getClientIp(),
            'telephoneNumber' => $billingAddress->getPhoneNumber(),
        ];
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}
