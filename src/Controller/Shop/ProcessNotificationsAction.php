<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\PaymentNotificationResolver;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProcessNotificationsAction
{
    public const EXPECTED_ADYEN_RESPONSE = '[accepted]';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var PaymentNotificationResolver */
    private $paymentNotificationResolver;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository,
        Dispatcher $dispatcher,
        PaymentNotificationResolver $paymentNotificationResolver
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentRepository = $paymentRepository;

        $this->dispatcher = $dispatcher;
        $this->paymentNotificationResolver = $paymentNotificationResolver;
    }

    private function validateRequest(string $code, array $arguments): void
    {
        // todo: prettify
        if (
            empty($arguments['notificationItems'])
            || !is_array($arguments['notificationItems'])
        ) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }
    }

    private function handleAction(PaymentInterface $payment, array $notificationItem)
    {
        try {
            $command = $this->dispatcher->getCommandFactory()->createForEvent($notificationItem['eventCode'], $payment);
            $this->dispatcher->dispatch($command);
        } catch (\InvalidArgumentException $ex) {
        }
    }

    public function __invoke(string $code, Request $request): Response
    {
        $arguments = $request->request->all();
        $this->validateRequest($code, $arguments);

        foreach ($arguments['notificationItems'] as $notificationItem) {
            $notificationItem = $notificationItem['NotificationRequestItem'];

            $payment = $this->paymentNotificationResolver->resolve($code, $notificationItem);

            if (!$payment) {
                continue;
            }

            $this->handleAction($payment, $notificationItem);
        }

        return new Response(self::EXPECTED_ADYEN_RESPONSE);
    }
}