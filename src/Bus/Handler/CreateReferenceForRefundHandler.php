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
use BitBag\SyliusAdyenPlugin\Factory\AdyenReferenceFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateReferenceForRefundHandler implements MessageHandlerInterface
{
    /** @var RepositoryInterface */
    private $adyenReferenceRepository;

    /** @var AdyenReferenceFactoryInterface */
    private $adyenReferenceFactory;

    public function __construct(
        RepositoryInterface $adyenReferenceRepository,
        AdyenReferenceFactoryInterface $adyenReferenceFactory
    ) {
        $this->adyenReferenceRepository = $adyenReferenceRepository;
        $this->adyenReferenceFactory = $adyenReferenceFactory;
    }

    public function __invoke(CreateReferenceForRefund $referenceCommand): void
    {
        $object = $this->adyenReferenceFactory->createForRefund(
            $referenceCommand->getRefundReference(),
            $referenceCommand->getPayment(),
            $referenceCommand->getRefundPayment()
        );
        $this->adyenReferenceRepository->add($object);
    }
}
