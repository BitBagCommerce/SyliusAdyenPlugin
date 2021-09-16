<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateReferenceForPayment;
use BitBag\SyliusAdyenPlugin\Factory\AdyenReferenceFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateReferenceForPaymentHandler implements MessageHandlerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $adyenReferenceRepository;
    /**
     * @var AdyenReferenceFactoryInterface
     */
    private $adyenReferenceFactory;

    public function __construct(
        RepositoryInterface $adyenReferenceRepository,
        AdyenReferenceFactoryInterface $adyenReferenceFactory
    ) {
        $this->adyenReferenceRepository = $adyenReferenceRepository;
        $this->adyenReferenceFactory = $adyenReferenceFactory;
    }

    public function __invoke(CreateReferenceForPayment $referenceCommand): void
    {
        $object = $this->adyenReferenceFactory->createForPayment($referenceCommand->getPayment());
        $this->adyenReferenceRepository->add($object);
    }
}
