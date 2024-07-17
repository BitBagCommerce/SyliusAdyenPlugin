<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\AlterPaymentCommand;
use BitBag\SyliusAdyenPlugin\Bus\Command\CancelPayment;
use BitBag\SyliusAdyenPlugin\Bus\Command\RequestCapture;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class AlterPaymentHandler
{
    use GatewayConfigFromPaymentTrait;

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    public function __construct(AdyenClientProviderInterface $adyenClientProvider)
    {
        $this->adyenClientProvider = $adyenClientProvider;
    }

    private function isCompleted(OrderInterface $order): bool
    {
        return PaymentInterface::STATE_COMPLETED === $order->getPaymentState();
    }

    private function isAdyenPayment(PaymentInterface $payment): bool
    {
        /**
         * @var ?PaymentMethodInterface $method
         */
        $method = $payment->getMethod();
        if (
            null === $method ||
            null === $method->getGatewayConfig() ||
            !isset($this->getGatewayConfig($method)->getConfig()[AdyenClientProviderInterface::FACTORY_NAME])
        ) {
            return false;
        }

        return true;
    }

    private function getPayment(OrderInterface $order): ?PaymentInterface
    {
        if ($this->isCompleted($order)) {
            return null;
        }

        $payment = $order->getLastPayment(PaymentInterface::STATE_AUTHORIZED);
        if (null === $payment) {
            return null;
        }

        return $payment;
    }

    private function dispatchRemoteAction(
        PaymentInterface $payment,
        AlterPaymentCommand $alterPaymentCommand,
        AdyenClientInterface $adyenClient,
    ): void {
        if ($alterPaymentCommand instanceof RequestCapture) {
            $adyenClient->requestCapture(
                $payment,
            );
        }

        if ($alterPaymentCommand instanceof CancelPayment) {
            $adyenClient->requestCancellation($payment);
        }
    }

    public function __invoke(AlterPaymentCommand $alterPaymentCommand): void
    {
        $payment = $this->getPayment($alterPaymentCommand->getOrder());

        if (null === $payment || !$this->isAdyenPayment($payment)) {
            return;
        }

        $method = $payment->getMethod();
        Assert::isInstanceOf($method, PaymentMethodInterface::class);

        $client = $this->adyenClientProvider->getForPaymentMethod($method);
        $this->dispatchRemoteAction($payment, $alterPaymentCommand, $client);
    }
}
