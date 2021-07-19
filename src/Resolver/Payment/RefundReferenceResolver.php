<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

class RefundReferenceResolver
{
    public const REFERENCE_PATTERN = '##%d-%s';

    /** @var RepositoryInterface */
    private $refundPaymentRepository;

    public function __construct(RepositoryInterface $refundPaymentRepository)
    {
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

        $result = $this->refundPaymentRepository->findOneBy([
            'orderNumber' => $orderNumber,
            'id' => $refundPaymentId
        ]);

        Assert::notNull($result);
        Assert::isInstanceOf($result, RefundPaymentInterface::class);

        return $result;
    }
}
