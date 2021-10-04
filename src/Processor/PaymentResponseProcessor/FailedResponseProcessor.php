<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class FailedResponseProcessor extends AbstractProcessor
{
    public const PAYMENT_REFUSED_CODES = ['refused', 'rejected', 'cancelled', 'error'];

    public const FAILURE_REDIRECT_TARGET = 'sylius_shop_order_show';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return $this->isResultCodeSupportedForPayment($payment, self::PAYMENT_REFUSED_CODES);
    }

    public function process(string $code, Request $request, PaymentInterface $payment): Response
    {
        /**
         * @var Session $session
         */
        $session = $request->getSession();
        $session->getFlashBag()->add(
            'error',
            $this->translator->trans('bitbag_sylius_adyen_plugin.ui.payment_failed')
        );

        $order = $payment->getOrder();
        Assert::notNull($order);

        return new RedirectResponse(
            $this->urlGenerator->generate(
                self::FAILURE_REDIRECT_TARGET,
                ['tokenValue' => $order->getTokenValue()]
            )
        );
    }
}
