<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Provider\SignatureValidatorProvider;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentRepository;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

class PaymentNotificationResolver
{
    /** @var SignatureValidatorProvider */
    private $signatureValidatorProvider;

    /** @var PaymentRepository */
    private $paymentRepository;

    public function __construct(
        SignatureValidatorProvider $signatureValidatorProvider,
        PaymentRepository $paymentRepository
    ) {
        $this->signatureValidatorProvider = $signatureValidatorProvider;
        $this->paymentRepository = $paymentRepository;
    }

    private function getMethodFromPayment(PaymentInterface $payment): PaymentMethodInterface
    {
        $result = $payment->getMethod();
        if ($result === null) {
            throw new \InvalidArgumentException(
                sprintf('Payment #%d has no method associated', $payment->getId())
            );
        }

        return $result;
    }

    public function resolve(string $gatewayCode, array $notificationItem): ?PaymentInterface
    {
        $signatureValidator = $this->signatureValidatorProvider->getValidatorForCode($gatewayCode);

        if (!$signatureValidator->isValid($notificationItem)) {
            return null;
        }

        /**
         * @var PaymentInterface|null $payment
         */
        $payment = $this->paymentRepository->find($notificationItem['merchantReference']);

        if ($payment === null) {
            return null;
        }

        if ($this->getMethodFromPayment($payment)->getCode() != $gatewayCode) {
            return null;
        }

        return $payment;
    }
}
