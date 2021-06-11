<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\MarkOrderPaidCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MarkOrderPaidCommandHandler implements MessageHandlerInterface
{


    public function __invoke(MarkOrderPaidCommand $data)
    {
        return;
    }
}
