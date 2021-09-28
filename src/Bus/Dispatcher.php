<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class Dispatcher implements DispatcherInterface
{
    use HandleTrait;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var PaymentCommandFactoryInterface */
    private $commandFactory;

    public function __construct(
        MessageBusInterface $messageBus,
        PaymentCommandFactoryInterface $commandFactory
    ) {
        $this->messageBus = $messageBus;
        $this->commandFactory = $commandFactory;
    }

    public function getCommandFactory(): PaymentCommandFactoryInterface
    {
        return $this->commandFactory;
    }

    /**
     * @return mixed
     */
    public function dispatch(object $action)
    {
        return $this->handle($action);
    }
}
