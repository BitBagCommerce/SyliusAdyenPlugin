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
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

class AlterPaymentHandler implements MessageHandlerInterface
{
    use GatewayConfigFromPaymentTrait;

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(AdyenClientProvider $adyenClientProvider)
    {
        $this->adyenClientProvider = $adyenClientProvider;
    }

    private function isCompleted(OrderInterface $order): bool
    {
        return $order->getPaymentState() == PaymentInterface::STATE_COMPLETED;
    }

    private function isAdyenPayment(PaymentInterface $payment): bool
    {
        /**
         * @var ?PaymentMethodInterface $method
         */
        $method = $payment->getMethod();
        if (
            $method === null
            || $method->getGatewayConfig() === null
            || !isset($this->getGatewayConfig($method)->getConfig()[AdyenClientProvider::FACTORY_NAME])
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
        if ($payment === null) {
            return null;
        }

        return $payment;
    }

    private function dispatchRemoteAction(
        string $pspReference,
        AlterPaymentCommand $alterPaymentCommand,
        AdyenClientInterface $adyenClient
    ): void {
        if ($alterPaymentCommand instanceof RequestCapture) {
            $adyenClient->requestCapture(
                $pspReference,
                $alterPaymentCommand->getOrder()->getTotal(),
                (string) $alterPaymentCommand->getOrder()->getCurrencyCode()
            );
        }

        if ($alterPaymentCommand instanceof CancelPayment) {
            $adyenClient->requestCancellation($pspReference);
        }
    }

    public function __invoke(AlterPaymentCommand $alterPaymentCommand): void
    {
        $payment = $this->getPayment($alterPaymentCommand->getOrder());

        if ($payment === null || !$this->isAdyenPayment($payment)) {
            return;
        }

        $details = $payment->getDetails();
        if (!isset($details['pspReference'])) {
            return;
        }

        $method = $payment->getMethod();
        Assert::isInstanceOf($method, PaymentMethodInterface::class);

        $client = $this->adyenClientProvider->getForPaymentMethod($method);
        $this->dispatchRemoteAction((string) $details['pspReference'], $alterPaymentCommand, $client);
    }
}
