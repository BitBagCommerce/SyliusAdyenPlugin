<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use BitBag\SyliusAdyenPlugin\Traits\PaymentFromOrderTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TakeOverPaymentHandler
{
    use PayableOrderPaymentTrait;
    use PaymentFromOrderTrait;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var EntityManagerInterface */
    private $paymentManager;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        EntityManagerInterface $paymentManager,
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentManager = $paymentManager;
    }

    private function persistPayment(PaymentInterface $payment): void
    {
        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();
    }

    public function __invoke(TakeOverPayment $command): void
    {
        $payment = $this->getPayablePayment($command->getOrder());
        $method = $this->getMethod($payment);

        if ($method->getCode() === $command->getPaymentCode()) {
            return;
        }

        $paymentMethod = $this->paymentMethodRepository->getOneForAdyenAndCode($command->getPaymentCode());
        $payment->setMethod($paymentMethod);

        $this->persistPayment($payment);
    }
}
