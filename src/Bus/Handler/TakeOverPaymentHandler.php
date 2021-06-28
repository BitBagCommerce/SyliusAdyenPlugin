<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\PayableOrderPaymentTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TakeOverPaymentHandler implements MessageHandlerInterface
{
    use PayableOrderPaymentTrait;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /** @var PaymentFactoryInterface */
    private $paymentFactory;

    /** @var EntityManagerInterface */
    private $paymentManager;

    /** @var EntityManagerInterface */
    private $orderManager;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentFactoryInterface $paymentFactory,
        EntityManagerInterface $paymentManager,
        EntityManagerInterface $orderManager
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentFactory = $paymentFactory;
        $this->paymentManager = $paymentManager;
        $this->orderManager = $orderManager;
    }

    private function getPaymentMethod(string $paymentCode): PaymentMethodInterface
    {
        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($paymentCode);

        if ($paymentMethod === null) {
            throw new \InvalidArgumentException(
                sprintf('Cannot get PaymentMethod with code "%s"', $paymentCode)
            );
        }

        return $paymentMethod;
    }

    private function persistPayment(PaymentInterface $payment): void
    {
        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();
    }

    public function __invoke(TakeOverPayment $command): void
    {
        $payment = $this->getPayablePayment($command->getOrder());
        if (
            $payment->getMethod() !== null
            && $payment->getMethod()->getCode() === $command->getPaymentCode()
        ) {
            return;
        }

        $paymentMethod = $this->getPaymentMethod($command->getPaymentCode());
        $payment->setMethod($paymentMethod);

        $this->persistPayment($payment);
    }
}
