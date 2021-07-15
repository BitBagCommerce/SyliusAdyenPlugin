<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;

class PaymentNotificationResolver implements CommandResolver
{
    /** @var Dispatcher */
    private $dispatcher;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    public function __construct(
        Dispatcher $dispatcher,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->dispatcher = $dispatcher;
        $this->paymentRepository = $paymentRepository;
    }

    private function fetchPayment(string $paymentCode, string $id): PaymentInterface
    {
        $payment = $this->paymentRepository->findOneByCodeAndId($paymentCode, (int) $id);

        if ($payment === null) {
            throw new NoCommandResolved();
        }

        return $payment;
    }

    public function resolve(string $paymentCode, array $notificationData): object
    {
        try {
            $payment = $this->fetchPayment($paymentCode, (string) $notificationData['merchantReference']);

            return $this->dispatcher->getCommandFactory()->createForEvent(
                (string) $notificationData['eventCode'],
                $payment,
                $notificationData
            );
        } catch (UnmappedAdyenActionException $ex) {
            throw new NoCommandResolved();
        }
    }
}
