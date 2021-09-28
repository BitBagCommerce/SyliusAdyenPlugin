<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Unit\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait AdyenClientTrait
{
    /** @var AdyenClientProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $adyenClientProvider;

    /** @var AdyenClientInterface|MockObject */
    private $adyenClient;

    private function setupAdyenClientMocks(): void
    {
        $this->adyenClient = $this->createMock(AdyenClientInterface::class);
        $this->adyenClientProvider = $this->createMock(AdyenClientProviderInterface::class);
        $this
            ->adyenClientProvider
            ->method('getForPaymentMethod')
            ->willReturn($this->adyenClient)
        ;
    }
}
