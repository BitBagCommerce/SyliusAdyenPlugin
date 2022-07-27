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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class FallbackResponseProcessor extends AbstractProcessor
{
    public const REDIRECT_TARGET_ACTION = 'bitbag_adyen_thank_you';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function accepts(Request $request, ?PaymentInterface $payment): bool
    {
        return null !== $payment;
    }

    public function process(
        string $code,
        Request $request,
        PaymentInterface $payment
    ): string
    {
        $tokenValue = $request->query->get('tokenValue');
        if (null === $tokenValue) {
            $this->setActiveOrderViaPayment($request, $payment);
        }

        return $this->urlGenerator->generate(
            self::REDIRECT_TARGET_ACTION,
            [
                'code' => $code,
                'tokenValue' => $tokenValue,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function setActiveOrderViaPayment(Request $request, PaymentInterface $payment): void
    {
        $order = $payment->getOrder();
        if (null === $order) {
            return;
        }

        $request->getSession()->set('sylius_order_id', $order->getId());
    }
}
