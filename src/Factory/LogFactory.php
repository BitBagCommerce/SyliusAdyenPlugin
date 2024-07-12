<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
        int $errorCode,
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
