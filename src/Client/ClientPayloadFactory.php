<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;


use BitBag\SyliusAdyenPlugin\Client\ClientPayloadFactory\OrderDataAssemblerInterface;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Exception\UnboundAddressFromOrderException;
use BitBag\SyliusAdyenPlugin\Resolver\Version\VersionResolverInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

final class ClientPayloadFactory implements ClientPayloadFactoryInterface
{
    /**
     * @var VersionResolverInterface
     */
    private $versionResolver;
    /**
     * @var OrderDataAssemblerInterface
     */
    private $orderDataAssembler;

    public function __construct(
        VersionResolverInterface $versionResolver,
        OrderDataAssemblerInterface $orderDataAssembler
    )
    {
        $this->versionResolver = $versionResolver;
        $this->orderDataAssembler = $orderDataAssembler;
    }

    private function getOrigin(string $url): string
    {
        $components = parse_url($url);

        $pattern = '%s://%s';
        if (isset($components['port'])) {
            $pattern .= ':%d';
        }

        return sprintf(
            $pattern,
            $components[AdyenClientInterface::CREDIT_CARD_TYPE] ?? '',
            $components['host'] ?? '',
            $components['port'] ?? 0
        );
    }

    private function isTokenizationSupported(array $payload, ?AdyenTokenInterface $customerIdentifier): bool
    {
        if ($customerIdentifier === null) {
            return false;
        }

        if (
            isset($payload['paymentMethod']['type'])
            && $payload['paymentMethod']['type'] !== AdyenClientInterface::CREDIT_CARD_TYPE
        ) {
            return false;
        }

        return true;
    }

    private function enableOneOffPayment(
        array $payload,
        ?AdyenTokenInterface $customerIdentifier,
        bool $store = false
    ): array {
        if (!$this->isTokenizationSupported($payload, $customerIdentifier)) {
            return $payload;
        }

        if ($store) {
            $payload['storePaymentMethod'] = true;
        }

        $payload['recurringProcessingModel'] = 'CardOnFile';
        $payload['shopperInteraction'] = 'Ecommerce';
        $payload['shopperReference'] = ($customerIdentifier === null ? '' : $customerIdentifier->getIdentifier());

        return $payload;
    }

    public function createForAvailablePaymentMethods(
        ArrayObject $options,
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array
    {
        $address = $order->getBillingAddress();
        $countryCode = $address !== null ? (string) $address->getCountryCode() : '';

        $payload = [
            'amount' => [
                'value' => $order->getTotal(),
                'currency' => (string)$order->getLocaleCode(),
            ],
            'merchantAccount' => $options['merchantAccount'],
            'countryCode' => $countryCode,
            'shopperLocale' => $countryCode,
        ];

        $payload = $this->enableOneOffPayment($payload, $adyenToken);
        $payload = $this->versionResolver->appendVersionConstraints($payload);

        return $payload;
    }

    public function createForPaymentDetails(
        array $receivedPayload,
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array
    {
        $payload = [
            'details'=>$receivedPayload
        ];

        $payload = $this->enableOneOffPayment($payload, $adyenToken);
        $payload = $this->versionResolver->appendVersionConstraints($payload);

        return $payload;
    }

    public function createForSubmitPayment(
        ArrayObject $options,
        string $url,
        array $receivedPayload,
        OrderInterface $order,
        ?AdyenTokenInterface $adyenToken = null
    ): array
    {
        $payload = [
            'amount' => [
                'value' => $order->getTotal(),
                'currency' => (string)$order->getCurrencyCode(),
            ],
            'reference' => (string) $order->getNumber(),
            'merchantAccount' => $options['merchantAccount'],
            'returnUrl' => $url,
            'paymentMethod' => $receivedPayload['paymentMethod'],
            'additionalData' => [
                'allow3DS2' => true,
            ],
            'channel' => 'web',
            'origin' => $this->getOrigin($url),
        ];

        if (isset($receivedPayload['browserInfo'])) {
            $payload['browserInfo'] = (array) $receivedPayload['browserInfo'];
        }

        $payload = $this->enableOneOffPayment(
            $payload,
            $adyenToken,
            (bool) ($receivedPayload['storePaymentMethod'] ?? false)
        );
        $payload = $this->versionResolver->appendVersionConstraints($payload);

        $payload = $payload + $this->orderDataAssembler->assemble($order);

        return $payload;
    }

    public function createForCapture(
        ArrayObject $options,
        PaymentInterface $payment
    ): array
    {
        $params = [
            'merchantAccount' => $options['merchantAccount'],
            'modificationAmount' => [
                'value' => $payment->getAmount(),
                'currency' => (string)$payment->getCurrencyCode(),
            ],
            'originalReference' => $payment->getDetails()['pspReference'],
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return $params;
    }

    public function createForCancel(
        ArrayObject $options,
        PaymentInterface $payment
    ): array
    {
        $params = [
            'merchantAccount' => $options['merchantAccount'],
            'originalReference' => $payment->getDetails()['pspReference'],
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return $params;
    }

    public function createForTokenRemove(
        ArrayObject $options,
        string $paymentReference,
        AdyenTokenInterface $adyenToken
    ): array
    {
        $params = [
            'merchantAccount' => $options['merchantAccount'],
            'recurringDetailReference' => $paymentReference,
            'shopperReference' => $adyenToken->getIdentifier(),
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return $params;
    }

    public function createForRefund(
        ArrayObject $options,
        PaymentInterface $payment,
        RefundPaymentGenerated $refund
    ): array
    {
        $params = [
            'merchantAccount' => $options['merchantAccount'],
            'modificationAmount' => [
                'value' => $refund->amount(),
                'currency' => $refund->currencyCode(),
            ],
            'reference' => $payment->getOrder()->getNumber(),
            'originalReference' => $payment->getDetails()['pspReference'],
        ];

        $params = $this->versionResolver->appendVersionConstraints($params);

        return $params;
    }
}