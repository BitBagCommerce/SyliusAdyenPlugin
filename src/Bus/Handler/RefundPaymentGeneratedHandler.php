<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\RefundReferenceResolver;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

class RefundPaymentGeneratedHandler implements MessageHandlerInterface
{
    use GatewayConfigFromPaymentTrait;

    /** @var RefundReferenceResolver */
    private $refundReferenceResolver;

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    public function __construct(
        RefundReferenceResolver $refundReferenceResolver,
        AdyenClientProvider $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $this->refundReferenceResolver = $refundReferenceResolver;
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function __invoke(RefundPaymentGenerated $paymentGenerated): void
    {
        $payment = $this->paymentRepository->find($paymentGenerated->paymentId());
        $paymentMethod = $this->paymentMethodRepository->find($paymentGenerated->paymentMethodId());

        if ($payment === null
            || $paymentMethod === null
            || !isset($this->getGatewayConfig($paymentMethod)->getConfig()[AdyenClientProvider::FACTORY_NAME])
        ) {
            return;
        }

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
        $reference = $this->refundReferenceResolver->createReference(
            $paymentGenerated->orderNumber(),
            $paymentGenerated->id()
        );

        Assert::keyExists($payment->getDetails(), 'pspReference');

        $client->requestRefund(
            (string) $payment->getDetails()['pspReference'],
            $paymentGenerated->amount(),
            $paymentGenerated->currencyCode(),
            $reference
        );
    }
}
