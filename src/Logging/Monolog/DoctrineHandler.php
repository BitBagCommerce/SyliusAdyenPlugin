<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Logging\Monolog;

use BitBag\SyliusAdyenPlugin\Factory\LogFactoryInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class DoctrineHandler extends AbstractProcessingHandler
{
    /** @var LogFactoryInterface */
    private $logFactory;

    /** @var RepositoryInterface */
    private $repository;

    public function __construct(
        LogFactoryInterface $logFactory,
        RepositoryInterface $repository,
    ) {
        $this->logFactory = $logFactory;
        $this->repository = $repository;

        parent::__construct();
    }

    protected function write(array $record): void
    {
        $log = $this->logFactory->create($record['message'], $record['level'], 0);

        $this->repository->add($log);
    }
}
