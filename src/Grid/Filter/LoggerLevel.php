<?php declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class LoggerLevel implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('level', $data['loggerLevel']));
    }
}
