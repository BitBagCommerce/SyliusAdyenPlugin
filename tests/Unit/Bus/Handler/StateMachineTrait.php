<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachine;

trait StateMachineTrait
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|FactoryInterface */
    private $stateMachineFactory;

    /** @var \PHPUnit\Framework\MockObject\MockObject|StateMachine */
    private $stateMachine;

    private function setupStateMachineMocks(): void
    {
        $this->stateMachine = $this->createMock(StateMachine::class);

        $this->stateMachineFactory = $this->createMock(FactoryInterface::class);
        $this->stateMachineFactory
            ->method('get')
            ->willReturn($this->stateMachine)
        ;
    }
}
