<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Provider\SignatureValidatorProvider;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentRepository;
use Sylius\Component\Core\Model\PaymentInterface;

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

    public function resolve(string $gatewayCode, array $notificationItem): ?PaymentInterface
    {
        $signatureValidator = $this->signatureValidatorProvider->getValidatorForCode($gatewayCode);

        if (!$signatureValidator->isValid($notificationItem)) {
            return null;
        }

        /**
         * @var $payment PaymentInterface
         */
        $payment = $this->paymentRepository->find($notificationItem['merchantReference']);

        if (!$payment) {
            return null;
        }

        if ($payment->getMethod()->getCode() != $gatewayCode) {
            return null;
        }

        return $payment;
    }
}
