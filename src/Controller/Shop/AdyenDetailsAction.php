<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Resolver\Payment\PaymentDetailsResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdyenDetailsAction
{
    public const REFERENCE_ID_KEY = 'referenceId';

    /** @var PaymentDetailsResolverInterface */
    private $paymentDetailsResolver;

    public function __construct(
        PaymentDetailsResolverInterface $paymentDetailsResolver
    ) {
        $this->paymentDetailsResolver = $paymentDetailsResolver;
    }

    public function __invoke(Request $request, string $code): Response
    {
        $referenceId = $request->query->get(self::REFERENCE_ID_KEY);

        if (null === $referenceId) {
            return new Response('Reference ID is missing', Response::HTTP_BAD_REQUEST);
        }

        $payment = $this->paymentDetailsResolver->resolve($code, $referenceId);

        return new JsonResponse($payment->getDetails());
    }
}
