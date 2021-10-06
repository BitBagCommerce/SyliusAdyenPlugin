<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor;
use BitBag\SyliusAdyenPlugin\Resolver\Payment\PaymentDetailsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTargetAction
{
    public const REDIRECT_RESULT_KEY = 'redirectResult';

    /** @var PaymentResponseProcessor */
    private $paymentResponseProcessor;

    /** @var PaymentDetailsResolverInterface */
    private $paymentDetailsResolver;

    public function __construct(
        PaymentResponseProcessor $paymentResponseProcessor,
        PaymentDetailsResolverInterface $paymentDetailsResolver
    ) {
        $this->paymentResponseProcessor = $paymentResponseProcessor;
        $this->paymentDetailsResolver = $paymentDetailsResolver;
    }

    public function __invoke(Request $request, string $code): Response
    {
        $payment = null;
        $referenceId = $this->getReferenceId($request);

        if ($referenceId !== null) {
            $payment = $this->paymentDetailsResolver->resolve($code, $referenceId);
        }

        return $this->paymentResponseProcessor->process($code, $request, $payment);
    }

    private function getReferenceId(Request $request): ?string
    {
        return $request->query->has(self::REDIRECT_RESULT_KEY)
            ? (string) $request->query->get(self::REDIRECT_RESULT_KEY)
            : null
        ;
    }
}
