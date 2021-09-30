<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Processor;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor\Processor;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class PaymentResponseProcessor implements PaymentResponseProcessorInterface
{
    private const DEFAULT_REDIRECT_ROUTE = 'sylius_shop_homepage';

    /** @var Processor[] */
    private $processors;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * PaymentResponseProcessor constructor.
     *
     * @param Processor[] $processors
     */
    public function __construct(
        iterable $processors,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->processors = $processors;
        $this->urlGenerator = $urlGenerator;
    }

    public function process(string $code, Request $request, ?PaymentInterface $payment): Response
    {
        foreach ($this->processors as $processor) {
            if (!$processor->accepts($request, $payment)) {
                continue;
            }

            Assert::notNull($payment);

            return $processor->process($code, $request, $payment);
        }

        return new RedirectResponse(
            $this->urlGenerator->generate(self::DEFAULT_REDIRECT_ROUTE)
        );
    }
}
