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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class FailedResponseProcessor extends AbstractProcessor
{
    use ProcessableResponseTrait;

    public const PAYMENT_REFUSED_CODES = ['refused', 'rejected', 'cancelled', 'error'];

    public const CHECKOUT_FINALIZATION_REDIRECT = 'sylius_shop_checkout_complete';

    public const FAILURE_REDIRECT_TARGET = 'sylius_shop_order_show';

    public const LABEL_PAYMENT_FAILED = 'bitbag_sylius_adyen_plugin.ui.payment_failed';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var DispatcherInterface */
    private $dispatcher;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        DispatcherInterface $dispatcher
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return $this->isResultCodeSupportedForPayment($payment, self::PAYMENT_REFUSED_CODES);
    }

    private function getRedirectUrl(OrderInterface $order): string
    {
        $tokenValue = $order->getTokenValue();

        if (null === $tokenValue) {
            return $this->urlGenerator->generate(self::CHECKOUT_FINALIZATION_REDIRECT);
        }

        return $this->urlGenerator->generate(
            self::FAILURE_REDIRECT_TARGET,
            ['tokenValue' => $order->getTokenValue()]
        );
    }

    public function process(
        string $code,
        Request $request,
        PaymentInterface $payment
    ): string {
        $this->addFlash($request, self::FLASH_ERROR, self::LABEL_PAYMENT_FAILED);

        $this->dispatchPaymentStatusReceived($payment);

        $order = $payment->getOrder();
        Assert::notNull($order);

        return $this->getRedirectUrl($order);
    }
}
