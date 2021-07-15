<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

class RefundPaymentGeneratedHandler implements MessageHandlerInterface
{
    use PaymentFromOrderTrait;
    use GatewayConfigFromPaymentTrait;

    /** @var RefundReferenceResolver */
    private $refundReferenceResolver;

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        RefundReferenceResolver $refundReferenceResolver,
        AdyenClientProvider $adyenClientProvider,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->refundReferenceResolver = $refundReferenceResolver;
        $this->adyenClientProvider = $adyenClientProvider;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(RefundPaymentGenerated $paymentGenerated): void
    {
        $order = $this->orderRepository->findOneByNumber($paymentGenerated->orderNumber());
        $payment = $this->getPayment($order, PaymentInterface::STATE_COMPLETED);
        $paymentMethod = $this->getMethod($payment);

        if (!isset($this->getGatewayConfig($paymentMethod)->getConfig()['adyen'])) {
            return;
        }

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
        $reference = $this->refundReferenceResolver->createReference(
            $paymentGenerated->orderNumber(),
            $paymentGenerated->id()
        );

        Assert::notEmpty($payment->getDetails()['pspReference']);

        $client->requestRefund(
            (string) $payment->getDetails()['pspReference'],
            $paymentGenerated->amount(),
            $paymentGenerated->currencyCode(),
            $reference
        );
    }
}
