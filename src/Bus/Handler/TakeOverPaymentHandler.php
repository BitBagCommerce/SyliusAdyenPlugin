<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\TakeOverPayment;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TakeOverPaymentHandler implements MessageHandlerInterface
{
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

    public function __invoke(TakeOverPayment $command)
    {
        $order = $command->getOrder();

        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);
        if ($payment->getMethod()->getCode() === $command->getPaymentCode()) {
            return;
        }

        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($command->getPaymentCode());

        if ($paymentMethod === null) {
            throw new \InvalidArgumentException(
                sprintf('Cannot get PaymentMethod with code "%s"', $command->getPaymentCode())
            );
        }

        $payment->setMethod($paymentMethod);

        $this->paymentManager->persist($payment);
        $this->paymentManager->flush();
    }
}
