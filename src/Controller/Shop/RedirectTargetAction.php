<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentRepositoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectTargetAction
{
    public const MY_ORDERS_ROUTE_NAME = 'sylius_shop_account_order_index';

    public const THANKS_ROUTE_NAME = 'sylius_shop_order_thank_you';

    public const AUTHORIZATION_CODE = 'AUTHORISED';

    public const PREPARATION_CODE = 'PREPARE';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        UrlGeneratorInterface $urlGenerator,
        Dispatcher $dispatcher,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->urlGenerator = $urlGenerator;

        $this->paymentRepository = $paymentRepository;
        $this->dispatcher = $dispatcher;
    }

    private function getReferenceId(Request $request): ?string
    {
        return $request->query->has('redirectResult') ? (string) $request->query->get('redirectResult') : null;
    }

    private function handleDetailsResponse(PaymentInterface $payment, array $result): bool
    {
        if (strtoupper((string) $result['resultCode']) !== self::AUTHORIZATION_CODE) {
            return false;
        }

        $command = $this->dispatcher->getCommandFactory()->createForEvent(self::PREPARATION_CODE, $payment);
        $this->dispatcher->dispatch($command);

        return true;
    }

    private function createPayloadForDetails(string $referenceId): array
    {
        return [
            'details' => [
                'redirectResult' => $referenceId
            ]
        ];
    }

    private function getPaymentForReference(string $reference): PaymentInterface
    {
        $payment = $this->paymentRepository->find($reference);
        if (!$payment instanceof PaymentInterface) {
            throw new \InvalidArgumentException(
                sprintf('Payment not found for reference "%s"', $reference)
            );
        }

        return $payment;
    }

    private function processPayment(string $code, string $referenceId): bool
    {
        $client = $this->adyenClientProvider->getClientForCode($code);
        $result = $client->paymentDetails($this->createPayloadForDetails($referenceId));
        $payment = $this->getPaymentForReference((string) $result['merchantReference']);

        return $this->handleDetailsResponse($payment, $result);
    }

    private function shouldTheAlternativeThanksPageBeShown(Request $request, bool $isPaid): bool
    {
        if ($request->query->get('tokenValue') !== null) {
            return true;
        }

        if (!$isPaid) {
            return false;
        }

        if ($request->getSession()->get('sylius_order_id') !== null) {
            return false;
        }

        return true;
    }

    public function __invoke(Request $request, string $code): Response
    {
        $paid = false;
        $targetRoute = self::THANKS_ROUTE_NAME;
        $referenceId = $this->getReferenceId($request);

        if ($referenceId !== null) {
            $paid = $this->processPayment($code, $referenceId);
        }

        if ($this->shouldTheAlternativeThanksPageBeShown($request, $paid)) {
            /**
             * @var Session $session
             */
            $session = $request->getSession();
            $session->getFlashBag()->add('info', 'sylius.payment.completed');
            $targetRoute = self::MY_ORDERS_ROUTE_NAME;
        }

        return new RedirectResponse(
            $this->urlGenerator->generate($targetRoute)
        );
    }
}
