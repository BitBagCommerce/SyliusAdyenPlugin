<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Repository\RefundPaymentRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

class RefundReferenceResolver
{
    public const REFERENCE_PATTERN = '##%d-%s';

    /** @var RefundPaymentRepositoryInterface */
    private $refundPaymentRepository;

    public function __construct(
        RefundPaymentRepositoryInterface $refundPaymentRepository
    ) {
        $this->refundPaymentRepository = $refundPaymentRepository;
    }

    public function createReference(string $orderNumber, int $refundPaymentId): string
    {
        return sprintf(self::REFERENCE_PATTERN, $refundPaymentId, $orderNumber);
    }

    public function resolve(string $reference): RefundPaymentInterface
    {
        sscanf($reference, self::REFERENCE_PATTERN, $refundPaymentId, $orderNumber);

        Assert::notEmpty($orderNumber);
        Assert::notEmpty($refundPaymentId);

        return $this->refundPaymentRepository->getForOrderNumberAndRefundPaymentId(
            (string) $orderNumber,
            (int) $refundPaymentId
        );
    }
}
