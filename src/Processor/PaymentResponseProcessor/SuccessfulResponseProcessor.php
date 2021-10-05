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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SuccessfulResponseProcessor extends AbstractProcessor
{
    public const MY_ORDERS_ROUTE_NAME = 'sylius_shop_account_order_index';

    public const THANKS_ROUTE_NAME = 'sylius_shop_order_thank_you';

    public const PAYMENT_STATUS_RECEIVED_CODE = 'payment_status_received';

    public const PAYMENT_PROCEED_CODES = ['authorised', 'received'];

    public const ORDER_ID_KEY = 'sylius_order_id';

    public const TOKEN_VALUE_KEY = 'tokenValue';

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        DispatcherInterface $dispatcher,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->dispatcher = $dispatcher;
        $this->urlGenerator = $urlGenerator;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return $this->isResultCodeSupportedForPayment($payment, self::PAYMENT_PROCEED_CODES);
    }

    public function process(string $code, Request $request, PaymentInterface $payment): Response
    {
        $targetRoute = self::THANKS_ROUTE_NAME;

        $this->dispatchPaymentStatusReceived($payment);

        if ($this->shouldTheAlternativeThanksPageBeShown($request)) {
            $this->addSuccessfulFlash($request);
            $targetRoute = self::MY_ORDERS_ROUTE_NAME;
        }

        return new RedirectResponse(
            $this->urlGenerator->generate($targetRoute)
        );
    }

    private function shouldTheAlternativeThanksPageBeShown(Request $request): bool
    {
        if ($request->query->get(self::TOKEN_VALUE_KEY) !== null) {
            return true;
        }

        if ($request->getSession()->get(self::ORDER_ID_KEY) !== null) {
            return false;
        }

        return true;
    }

    private function dispatchPaymentStatusReceived(PaymentInterface $payment): void
    {
        $command = $this->dispatcher->getCommandFactory()->createForEvent(self::PAYMENT_STATUS_RECEIVED_CODE, $payment);
        $this->dispatcher->dispatch($command);
    }

    private function addSuccessfulFlash(Request $request): void
    {
        /**
         * @var Session $session
         */
        $session = $request->getSession();
        $session->getFlashBag()->add('info', 'sylius.payment.completed');
    }
}
