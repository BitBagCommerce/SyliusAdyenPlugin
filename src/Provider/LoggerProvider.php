<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Provider;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerProvider implements LoggerProviderInterface
{
    /** @var HandlerInterface */
    private $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function getLogger(): LoggerInterface
    {
        return new Logger('Adyen', [$this->handler]);
    }
}
