<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\AdyenReferenceInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

class AdyenReferenceFactory implements AdyenReferenceFactoryInterface
{
    private FactoryInterface $baseFactory;

    public function __construct(FactoryInterface $baseFactory)
    {
        $this->baseFactory = $baseFactory;
    }

    public function createForPayment(PaymentInterface $payment): AdyenReferenceInterface
    {
        $details = $payment->getDetails();
        Assert::keyExists($details, 'pspReference', 'Payment does not contain pspReference');

        $result = $this->createNew();
        $result->setPayment($payment);
        $result->setPspReference((string) $details['pspReference']);

        return $result;
    }

    public function createForRefund(
        string $reference,
        PaymentInterface $payment,
        RefundPaymentInterface $refundPayment
    ): AdyenReferenceInterface {
        $result = $this->createNew();
        $result->setPayment($payment);
        $result->setRefundPayment($refundPayment);
        $result->setPspReference($reference);

        return $result;
    }

    public function createNew(): AdyenReferenceInterface
    {
        /**
         * @var AdyenReferenceInterface $result
         */
        $result = $this->baseFactory->createNew();

        return $result;
    }
}
