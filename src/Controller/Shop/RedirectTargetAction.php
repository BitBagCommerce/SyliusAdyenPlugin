<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Exception\PaymentMethodForReferenceNotFoundException;
use BitBag\SyliusAdyenPlugin\Exception\UnprocessablePaymentException;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectTargetAction
{
    public const MY_ORDERS_ROUTE_NAME = 'sylius_shop_account_order_index';

    public const THANKS_ROUTE_NAME = 'sylius_shop_order_thank_you';

    public const PAYMENT_PROCEED_CODES = ['authorised', 'received'];

    public const MARK_ORDER_AS_COMPLETED_CODE = 'mark_order_as_completed';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Dispatcher */
    private $dispatcher;
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        UrlGeneratorInterface $urlGenerator,
        Dispatcher $dispatcher,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->urlGenerator = $urlGenerator;

        $this->dispatcher = $dispatcher;
        $this->orderRepository = $orderRepository;
    }

    private function getReferenceId(Request $request): ?string
    {
        return $request->query->has('redirectResult') ? (string) $request->query->get('redirectResult') : null;
    }

    private function handleDetailsResponse(PaymentInterface $payment, array $result): bool
    {
        if (!in_array(strtolower((string) $result['resultCode']), self::PAYMENT_PROCEED_CODES, true)) {
            return false;
        }

        $command = $this->dispatcher->getCommandFactory()->createForEvent(self::MARK_ORDER_AS_COMPLETED_CODE, $payment);
        $this->dispatcher->dispatch($command);

        return true;
    }

    private function createPayloadForDetails(string $referenceId): array
    {
        return [
            'details' => [
                'redirectResult' => $referenceId,
            ],
        ];
    }

    private function getPaymentForReference(string $orderNumber): PaymentInterface
    {
        /**
         * @var ?OrderInterface $order
         */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        if ($order === null) {
            throw new PaymentMethodForReferenceNotFoundException($orderNumber);
        }

        $payment = $order->getLastPayment();
        if ($payment === null) {
            throw new UnprocessablePaymentException();
        }

        return $payment;
    }

    private function processPayment(string $code, string $referenceId): bool
    {
        $client = $this->adyenClientProvider->getClientForCode($code);
        $result = $client->paymentDetails($this->createPayloadForDetails($referenceId));
        $payment = $this->getPaymentForReference((string) $result['merchantReference']);
        $payment->setDetails($result);

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
