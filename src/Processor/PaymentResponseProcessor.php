<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Processor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\ProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaymentResponseProcessor implements PaymentResponseProcessorInterface
{
    private const DEFAULT_REDIRECT_ROUTE = 'sylius_shop_order_thank_you';

    /** @var ProcessorInterface[] */
    private $processors;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(
        iterable $processors,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->processors = $processors;
        $this->urlGenerator = $urlGenerator;
    }

    private function processForPaymentSpecified(
        string $code,
        Request $request,
        PaymentInterface $payment
    ): ?string
    {
        foreach ($this->processors as $processor) {
            if (!$processor->accepts($request, $payment)) {
                continue;
            }

            return $processor->process($code, $request, $payment);
        }

        return null;
    }

    public function process(
        string $code,
        Request $request,
        ?PaymentInterface $payment
    ): string
    {
        $result = null;
        if (null !== $payment) {
            $result = $this->processForPaymentSpecified($code, $request, $payment);
        }

        if (null !== $result) {
            return $result;
        }

        return $this->urlGenerator->generate(self::DEFAULT_REDIRECT_ROUTE);
    }
}
