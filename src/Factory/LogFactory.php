<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Factory;

use BitBag\SyliusAdyenPlugin\Entity\LogInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class LogFactory implements FactoryInterface, LogFactoryInterface
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createNew()
    {
        return $this->factory->createNew();
    }

    public function create(
        string $message,
        int $level,
        int $errorCode
    ): LogInterface {
        /** @var LogInterface $log */
        $log = $this->createNew();

        $log->setMessage($message);
        $log->setLevel($level);
        $log->setErrorCode($errorCode);
        $log->setDateTime(new \DateTime());

        return $log;
    }
}
