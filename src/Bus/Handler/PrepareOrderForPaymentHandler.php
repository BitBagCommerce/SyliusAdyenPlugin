<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\PrepareOrderForPayment;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PrepareOrderForPaymentHandler implements MessageHandlerInterface
{
    /** @var OrderNumberAssignerInterface */
    private $orderNumberAssigner;

    private EntityRepository $orderRepository;

    public function __construct(
        OrderNumberAssignerInterface $orderNumberAssigner,
        EntityRepository $orderRepository
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
