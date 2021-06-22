<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectTargetAction
{
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
        return $request->query->get('redirectResult');
    }

    private function handleDetailsResponse(PaymentInterface $payment, array $result)
    {
        if ($result['resultCode'] !== self::AUTHORIZATION_CODE) {
            return;
        }

        $command = $this->dispatcher->getCommandFactory()->createForEvent(self::PREPARATION_CODE, $payment);
        $this->dispatcher->dispatch($command);
    }

    private function createPayloadForDetails(string $referenceId): array
    {
        return [
            'details'=>[
                'redirectResult'=>$referenceId
            ]
        ];
    }

    private function processPayment(string $code, string $referenceId)
    {
        $client = $this->adyenClientProvider->getClientForCode($code);
        $result = $client->paymentDetails($this->createPayloadForDetails($referenceId));
        $payment = $this->paymentRepository->find($result['merchantReference']);
        $this->handleDetailsResponse($payment, $result);
    }

    public function __invoke(Request $request, string $code): Response
    {
        $referenceId = $this->getReferenceId($request);
        if ($referenceId) {
            $this->processPayment($code, $referenceId);
        }

        return new RedirectResponse(
            $this->urlGenerator->generate(self::THANKS_ROUTE_NAME)
        );
    }
}
