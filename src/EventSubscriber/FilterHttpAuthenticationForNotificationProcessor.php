<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\EventSubscriber;

use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class FilterHttpAuthenticationForNotificationProcessor implements EventSubscriberInterface
{
    public const ROUTE_NAME = 'bitbag_adyen_process_notifications';

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'filterAuthentication'
        ];
    }

    private function getGatewayConfig(PaymentMethodInterface $paymentMethod): GatewayConfigInterface
    {
        $result = $paymentMethod->getGatewayConfig();
        if ($result === null) {
            throw new \InvalidArgumentException(
                sprintf('PaymentMethod %d has no gateway config', $paymentMethod->getId())
            );
        }

        return $result;
    }

    private function getConfiguration(string $code): array
    {
        $paymentMethod = $this->paymentMethodRepository->findOneForAdyenAndCode($code);
        if ($paymentMethod === null) {
            throw new NotFoundHttpException();
        }

        return $this->getGatewayConfig($paymentMethod)->getConfig();
    }

    private function isAuthenticated(Request $request, array $configuration): bool
    {
        if (!isset($configuration['authUser']) && !isset($configuration['authPassword'])) {
            return true;
        }

        if (
            $request->getUser() === $configuration['authUser']
            && $request->getPassword() === $configuration['authPassword']
        ) {
            return true;
        }

        return false;
    }

    public function filterAuthentication(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();
        if ($request->attributes->get('_route') !== self::ROUTE_NAME) {
            return;
        }

        $code = $request->attributes->get('code');
        $configuration = $this->getConfiguration($code);

        if ($this->isAuthenticated($request, $configuration)) {
            return;
        }

        throw new HttpException(Response::HTTP_FORBIDDEN);
    }
}
