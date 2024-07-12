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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SuccessfulResponseProcessor extends AbstractProcessor
{
    use ProcessableResponseTrait;

    public const MY_ORDERS_ROUTE_NAME = 'sylius_shop_account_order_index';

    public const THANKS_ROUTE_NAME = 'sylius_shop_order_thank_you';

    public const PAYMENT_PROCEED_CODES = ['authorised'];

    public const ORDER_ID_KEY = 'sylius_order_id';

    public const TOKEN_VALUE_KEY = 'tokenValue';

    public const LABEL_PAYMENT_COMPLETED = 'sylius.payment.completed';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        DispatcherInterface $dispatcher,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
    ) {
        $this->dispatcher = $dispatcher;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return $this->isResultCodeSupportedForPayment($payment, self::PAYMENT_PROCEED_CODES);
    }

    public function process(
        string $code,
        Request $request,
        PaymentInterface $payment,
    ): string {
        $targetRoute = self::THANKS_ROUTE_NAME;

        $this->dispatchPaymentStatusReceived($payment);

        if ($this->shouldTheAlternativeThanksPageBeShown($request)) {
            $this->addFlash($request, self::FLASH_INFO, self::LABEL_PAYMENT_COMPLETED);
            $targetRoute = self::MY_ORDERS_ROUTE_NAME;
        }

        return $this->urlGenerator->generate($targetRoute);
    }

    private function shouldTheAlternativeThanksPageBeShown(Request $request): bool
    {
        if (null !== $request->query->get(self::TOKEN_VALUE_KEY)) {
            return true;
        }

        if (null !== $request->getSession()->get(self::ORDER_ID_KEY)) {
            return false;
        }

        return true;
    }
}
