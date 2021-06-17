<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Actions\AdyenAction;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Provider\SignatureValidatorProvider;
use Sylius\Component\Core\Model\PaymentInterface;

use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessNotificationsAction
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var SignatureValidatorProvider */
    private $signatureValidatorProvider;
    /**
     * @var ServiceLocator
     */
    private $actions;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository,
        SignatureValidatorProvider $signatureValidatorProvider,
        ServiceLocator $actions
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentRepository = $paymentRepository;
        $this->signatureValidatorProvider = $signatureValidatorProvider;
        $this->actions = $actions;
    }

    private function validateRequest(array $arguments): void
    {
        // todo: prettify
        if (
            empty($arguments['notificationItems'])
            || !is_array($arguments['notificationItems'])
        ) {
            throw new \HttpException(Response::HTTP_BAD_REQUEST);
        }
    }

    public function __invoke(string $code, Request $request): Response
    {
        $arguments = $request->request->all();
        $this->validateRequest($arguments);

        $signatureValidator = $this->signatureValidatorProvider->getValidatorForCode($code);

        foreach ($arguments['notificationItems'] as $notificationItem) {
            $notificationItem = $notificationItem['NotificationRequestItem'];

            if (!$signatureValidator->isValid($notificationItem)) {
                continue;
            }

            // todo: check if order has been already paid
            // todo: check if it's a payment authorization

            /**
             * @var $payment PaymentInterface
             */
            $payment = $this->paymentRepository->find($notificationItem['merchantReference']);

            if (!$payment) {
                continue;
            }

            if ($payment->getMethod()->getCode() != $code) {
                continue;
            }

            try {
                /**
                 * @var $action AdyenAction
                 */
                $action = $this->actions->get($notificationItem['eventName']);
                if($action->accept($payment)){
                    $action($payment, $notificationItem);
                }
            }catch(ServiceNotFoundException $ex){
                continue;
            }
        }

        return new Response('[accepted]');
    }
}
