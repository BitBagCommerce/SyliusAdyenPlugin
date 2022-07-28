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

final class PaymentProcessingResponseProcessor extends AbstractProcessor
{
    use ProcessableResponseTrait;

    public const PAYMENT_PROCESSING_CODES = ['received', 'processing'];

    public const LABEL_PROCESSING = 'bitbag_sylius_adyen_plugin.ui.payment_processing';

    public const REDIRECT_TARGET_ROUTE = 'sylius_shop_homepage';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        DispatcherInterface $dispatcher,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->dispatcher = $dispatcher;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return $this->isResultCodeSupportedForPayment($payment, self::PAYMENT_PROCESSING_CODES);
    }

    public function process(
        string $code,
        Request $request,
        PaymentInterface $payment
    ): string {
        $this->dispatchPaymentStatusReceived($payment);
        $this->addFlash($request, self::FLASH_INFO, self::LABEL_PROCESSING);

        return $this->urlGenerator->generate(self::REDIRECT_TARGET_ROUTE);
    }
}
