<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Processor;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Exception\UnmappedAdyenActionException;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

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
        try {
            return $this->paymentRepository->getOneByCodeAndId($paymentCode, (int) $id);
        } catch (NoResultException $ex) {
            throw new NoCommandResolvedException();
        }
    }

    public function resolve(string $paymentCode, array $notificationData): object
    {
        try {
            Assert::keyExists($notificationData, 'merchantReference');

            $payment = $this->fetchPayment($paymentCode, (string) $notificationData['merchantReference']);

            return $this->dispatcher->getCommandFactory()->createForEvent(
                (string) $notificationData['eventCode'],
                $payment,
                $notificationData
            );
        } catch (UnmappedAdyenActionException $ex) {
            throw new NoCommandResolvedException();
        }
    }
}
