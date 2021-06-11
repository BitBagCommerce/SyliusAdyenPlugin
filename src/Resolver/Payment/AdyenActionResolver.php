<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Bus\Command\MarkOrderPaidCommand;
use BitBag\SyliusAdyenPlugin\Bus\CommandInterface;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

class AdyenActionResolver
{
    public const ACTIONS_MAPPING = [
        'Authorised' => MarkOrderPaidCommand::class
    ];

    public function resolve(OrderInterface $order, string $status, ?array $response = null): ?CommandInterface
    {
        /**
         * @var $actionClass CommandInterface
         */
        $actionClass = self::ACTIONS_MAPPING[$status] ?? null;
        if (!$actionClass) {
            throw new UnmappedAdyenActionException();
        }

        Assert::true(is_subclass_of($actionClass, CommandInterface::class));

        return $actionClass::createForOrder($order, $response);
    }
}
