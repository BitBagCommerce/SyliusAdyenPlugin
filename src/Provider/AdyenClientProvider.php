<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use BitBag\SyliusAdyenPlugin\Client\AdyenClient;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Psr\Http\Client\ClientInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

class AdyenClientProvider
{
    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelContextInterface $channelContext,
        ClientInterface $httpClient
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->channelContext = $channelContext;
        $this->httpClient = $httpClient;
    }

    public function getDefaultClient(): AdyenClient
    {
        $paymentMethod = $this->paymentMethodRepository->findOneByChannel(
            $this->channelContext->getChannel()
        );

        if (null === $paymentMethod) {
            throw new UpdateHandlingException(sprintf('No Adyen provider is configured'));
        }

        $gateway = $paymentMethod->getGatewayConfig();
        $config = $gateway->getConfig();

        return new AdyenClient($config, $this->httpClient);
    }

    public function getForPaymentMethod(PaymentMethodInterface $paymentMethod): AdyenClient
    {
        $isAdyen = $paymentMethod->getGatewayConfig()->getConfig()['adyen'] ?? null;
        if (!$isAdyen) {
            throw new \InvalidArgumentException(sprintf(
                'Provided PaymentMethod #%d is not an Adyen instance',
                $paymentMethod->getId()
            ));
        }

        return new AdyenClient($paymentMethod->getGatewayConfig()->getConfig(), $this->httpClient);
    }

    public function getClientForCode(string $code): AdyenClient
    {
        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($code);

        if (!$paymentMethod) {
            throw new \InvalidArgumentException(sprintf('Adyen for "%s" code is not configured', $code));
        }

        return $this->getForPaymentMethod($paymentMethod);
    }
}
