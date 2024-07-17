<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PrepareOrderForPayment;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PrepareOrderForPaymentHandler
{
    /** @var OrderNumberAssignerInterface */
    private $orderNumberAssigner;

    /** @var RepositoryInterface */
    private $orderRepository;

    public function __construct(
        OrderNumberAssignerInterface $orderNumberAssigner,
        RepositoryInterface $orderRepository,
    ) {
        $this->orderNumberAssigner = $orderNumberAssigner;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(PrepareOrderForPayment $command): void
    {
        $this->orderNumberAssigner->assignNumber($command->getOrder());
        $this->orderRepository->add($command->getOrder());
    }
}
