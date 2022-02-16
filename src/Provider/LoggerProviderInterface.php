<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use Psr\Log\LoggerInterface;

interface LoggerProviderInterface
{
    public function getLogger(): LoggerInterface;
}
