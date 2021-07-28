<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\EventSubscriber;

use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Traits\GatewayConfigFromPaymentTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class FilterHttpAuthenticationForNotificationProcessor implements EventSubscriberInterface
{
    use GatewayConfigFromPaymentTrait;

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

        $code = (string) $request->attributes->get('code');
        $configuration = $this->getConfiguration($code);

        if ($this->isAuthenticated($request, $configuration)) {
            return;
        }

        throw new HttpException(Response::HTTP_FORBIDDEN);
    }
}
