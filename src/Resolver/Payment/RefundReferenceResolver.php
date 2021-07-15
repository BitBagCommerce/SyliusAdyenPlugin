<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Webmozart\Assert\Assert;

class RefundReferenceResolver
{
    public const REFERENCE_PATTERN = '##%s-%d##';

    /** @var RepositoryInterface */
    private $refundPaymentRepository;

    public function __construct(RepositoryInterface $refundPaymentRepository)
    {
        $this->refundPaymentRepository = $refundPaymentRepository;
    }

    public function createReference(string $orderNumber, int $refundPaymentId): string
    {
        return sprintf(self::REFERENCE_PATTERN, $orderNumber, $refundPaymentId);
    }

    public function resolve(string $reference): RefundPaymentInterface
    {
        [$orderNumber, $refundPaymentId] = (array) sscanf(self::REFERENCE_PATTERN, $reference);

        Assert::notEmpty($orderNumber);
        Assert::notEmpty($refundPaymentId);

        $result = $this->refundPaymentRepository->findOneBy([
            'orderNumber' => $orderNumber,
            'id' => $refundPaymentId
        ]);

        Assert::notNull($result);

        return $result;
    }
}
