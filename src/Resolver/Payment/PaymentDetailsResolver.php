<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Payment;

use BitBag\SyliusAdyenPlugin\Exception\PaymentMethodForReferenceNotFoundException;
use BitBag\SyliusAdyenPlugin\Exception\UnprocessablePaymentException;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class PaymentDetailsResolver implements PaymentDetailsResolverInterface
{
    /** @var OrderRepository */
    private $orderRepository;

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    public function __construct(
        OrderRepository $orderRepository,
        AdyenClientProviderInterface $adyenClientProvider,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->adyenClientProvider = $adyenClientProvider;
        $this->paymentRepository = $paymentRepository;
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

    public function resolve(string $code, string $referenceId): PaymentInterface
    {
        $client = $this->adyenClientProvider->getClientForCode($code);
        $result = $client->paymentDetails($this->createPayloadForDetails($referenceId));
        $payment = $this->getPaymentForReference((string) $result['merchantReference']);
        $payment->setDetails($result);

        $this->paymentRepository->add($payment);

        return $payment;
    }
}
