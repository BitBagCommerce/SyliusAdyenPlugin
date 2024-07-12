<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor;

use BitBag\SyliusAdyenPlugin\Bus\DispatcherInterface;
use Sylius\Component\Core\Model\PaymentInterface;

trait ProcessableResponseTrait
{
    /** @var DispatcherInterface */
    private $dispatcher;

    protected function dispatchPaymentStatusReceived(PaymentInterface $payment): void
    {
        $command = $this->dispatcher->getCommandFactory()->createForEvent(self::PAYMENT_STATUS_RECEIVED_CODE, $payment);
        $this->dispatcher->dispatch($command);
    }
}
