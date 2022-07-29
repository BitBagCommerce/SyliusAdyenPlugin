<?php

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
        RepositoryInterface $repository
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
