<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class Dispatcher
{
    use HandleTrait;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var CommandFactory */
    private $commandFactory;

    public function __construct(
        MessageBusInterface $messageBus,
        CommandFactory $commandFactory
    ) {
        $this->messageBus = $messageBus;
        $this->commandFactory = $commandFactory;
    }

    public function getCommandFactory(): CommandFactory
    {
        return $this->commandFactory;
    }

    /**
     * @param mixed $action
     *
     * @return mixed
     */
    public function dispatch($action)
    {
        return $this->handle($action);
    }
}
