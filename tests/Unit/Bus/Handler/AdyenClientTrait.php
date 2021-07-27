<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use PHPUnit\Framework\MockObject\MockObject;

trait AdyenClientTrait
{
    /** @var AdyenClientProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $adyenClientProvider;

    /** @var AdyenClientInterface|MockObject */
    private $adyenClient;

    private function setupAdyenClientMocks(): void
    {
        $this->adyenClient = $this->createMock(AdyenClientInterface::class);
        $this->adyenClientProvider = $this->createMock(AdyenClientProvider::class);
        $this
            ->adyenClientProvider
            ->method('getForPaymentMethod')
            ->willReturn($this->adyenClient)
        ;
    }
}
