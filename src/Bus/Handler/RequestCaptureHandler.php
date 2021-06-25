<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use BitBag\SyliusAdyenPlugin\Bus\Command\RequestCapture;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RequestCaptureHandler implements MessageHandlerInterface
{
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
         * @var $method PaymentMethodInterface
         */
        $method = $payment->getMethod();
        if (
            $method === null
            || !isset($method->getGatewayConfig()->getConfig()[AdyenGatewayFactory::FACTORY_NAME])
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

    public function __invoke(RequestCapture $requestCapture)
    {
        $payment = $this->getPayment($requestCapture->getOrder());

        if ($payment === null || !$this->isAdyenPayment($payment)) {
            return;
        }

        $details = $payment->getDetails();
        if (!isset($details['pspReference'])) {
            return;
        }

        $client = $this->adyenClientProvider->getForPaymentMethod($payment->getMethod());
        $client->requestCapture(
            $details['pspReference'],
            $requestCapture->getOrder()->getTotal(),
            $requestCapture->getOrder()->getCurrencyCode()
        );
    }
}
