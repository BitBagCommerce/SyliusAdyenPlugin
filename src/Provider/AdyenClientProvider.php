<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\AdyenClient;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Client\AdyenTransportFactory;
use BitBag\SyliusAdyenPlugin\Client\ClientPayloadFactoryInterface;
use BitBag\SyliusAdyenPlugin\Client\PaymentMethodsFilterInterface;
use BitBag\SyliusAdyenPlugin\Exception\NonAdyenPaymentMethodException;
use BitBag\SyliusAdyenPlugin\Exception\UnprocessablePaymentException;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

final class AdyenClientProvider implements AdyenClientProviderInterface
{
    use GatewayConfigFromPaymentTrait;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var AdyenTransportFactory */
    private $adyenTransportFactory;

    /** @var ClientPayloadFactoryInterface */
    private $clientPayloadFactory;

    /** @var PaymentMethodsFilterInterface */
    private $paymentMethodsFilter;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelContextInterface $channelContext,
        AdyenTransportFactory $adyenTransportFactory,
        ClientPayloadFactoryInterface $clientPayloadFactory,
        PaymentMethodsFilterInterface $paymentMethodsFilter
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->channelContext = $channelContext;
        $this->adyenTransportFactory = $adyenTransportFactory;
        $this->clientPayloadFactory = $clientPayloadFactory;
        $this->paymentMethodsFilter = $paymentMethodsFilter;
    }

    public function getDefaultClient(): AdyenClientInterface
    {
        $paymentMethod = $this->paymentMethodRepository->findOneByChannel(
            $this->channelContext->getChannel()
        );

        if (null === $paymentMethod) {
            throw new UpdateHandlingException(sprintf('No Adyen provider is configured'));
        }

        $config = $this->getGatewayConfig($paymentMethod)->getConfig();

        return new AdyenClient(
            $config,
            $this->adyenTransportFactory,
            $this->clientPayloadFactory,
            $this->paymentMethodsFilter
        );
    }

    public function getForPaymentMethod(PaymentMethodInterface $paymentMethod): AdyenClientInterface
    {
        $gatewayConfig = $this->getGatewayConfig($paymentMethod);
        $isAdyen = isset($gatewayConfig->getConfig()[self::FACTORY_NAME]);
        if (!$isAdyen) {
            throw new NonAdyenPaymentMethodException($paymentMethod);
        }

        return new AdyenClient(
            $gatewayConfig->getConfig(),
            $this->adyenTransportFactory,
            $this->clientPayloadFactory,
            $this->paymentMethodsFilter
        );
    }

    public function getClientForCode(string $code): AdyenClientInterface
    {
        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($code);

        if (null === $paymentMethod) {
            throw new UnprocessablePaymentException();
        }

        return $this->getForPaymentMethod($paymentMethod);
    }
}
