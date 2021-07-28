<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
