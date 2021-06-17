<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Provider\SignatureValidatorProvider;
use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessNotificationsAction
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var SignatureValidatorProvider */
    private $signatureValidatorProvider;

    /** @var EntityManagerInterface */
    private $orderManager;

    public function __construct(
        AdyenClientProvider $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        SignatureValidatorProvider $signatureValidatorProvider,
        EntityManagerInterface $orderManager
    ) {
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentRepository = $paymentRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->signatureValidatorProvider = $signatureValidatorProvider;
        $this->orderManager = $orderManager;
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

            $payment->setState(PaymentInterface::STATE_COMPLETED);
            $order = $payment->getOrder();

            foreach ([
                 OrderPaymentTransitions::GRAPH => OrderPaymentTransitions::TRANSITION_PAY
             ] as $graph=>$state) {
                $stateMachine = $this->stateMachineFactory->get($order, $graph);
                $stateMachine->can($state) && $stateMachine->apply($state);
            }

            $this->orderManager->persist($order);
            $this->orderManager->flush();
        }

        return new Response('[accepted]');
    }
}
