<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateReferenceForRefund;
use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\RefundPaymentRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class RefundPaymentGeneratedHandler implements MessageHandlerInterface
{
    use GatewayConfigFromPaymentTrait;

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var RefundPaymentRepositoryInterface */
    private $refundPaymentRepository;

    /** @var DispatcherInterface */
    private $dispatcher;

    public function __construct(
        AdyenClientProviderInterface $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        RefundPaymentRepositoryInterface $refundPaymentRepository,
        DispatcherInterface $dispatcher,
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->refundPaymentRepository = $refundPaymentRepository;
        $this->dispatcher = $dispatcher;
    }

    private function createReference(
        string $newReference,
        RefundPaymentGenerated $refundPaymentGenerated,
        PaymentInterface $payment,
    ): void {
        $refund = $this->refundPaymentRepository->find($refundPaymentGenerated->id());
        if (null === $refund) {
            return;
        }

        $this->dispatcher->dispatch(new CreateReferenceForRefund($newReference, $refund, $payment));
    }

    private function sendRefundRequest(
        RefundPaymentGenerated $refundPaymentGenerated,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment,
    ): string {
        Assert::keyExists(
            $payment->getDetails(),
            'pspReference',
            'Payment has not been initialized by Adyen',
        );

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);

        $result = $client->requestRefund(
            $payment,
            $refundPaymentGenerated,
        );

        Assert::keyExists($result, 'pspReference');

        return (string) $result['pspReference'];
    }

    public function __invoke(RefundPaymentGenerated $refundPaymentGenerated): void
    {
        $payment = $this->paymentRepository->find($refundPaymentGenerated->paymentId());
        $paymentMethod = $this->paymentMethodRepository->find($refundPaymentGenerated->paymentMethodId());

        if (null === $payment ||
            null === $paymentMethod ||
            !isset($this->getGatewayConfig($paymentMethod)->getConfig()[AdyenClientProviderInterface::FACTORY_NAME])
        ) {
            return;
        }

        $adyenReference = $this->sendRefundRequest($refundPaymentGenerated, $paymentMethod, $payment);
        $this->createReference($adyenReference, $refundPaymentGenerated, $payment);
    }
}
