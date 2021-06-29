<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PaymentCheckoutOrderResolver implements PaymentCheckoutOrderResolverInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var RepositoryInterface */
    private $orderRepository;

    public function __construct(
        RequestStack $requestStack,
        CartContextInterface $cartContext,
        RepositoryInterface $orderRepository
    ) {
        $this->requestStack = $requestStack;
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
    }

    private function getCurrentRequest(): Request
    {
        $result = $this->requestStack->getCurrentRequest();
        if ($result === null) {
            throw new \InvalidArgumentException('No request available in stack');
        }

        return $result;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     */
    private function getCurrentOrder(): ?OrderInterface
    {
        /**
         * @var string|null $tokenValue
         */
        $tokenValue = $this->getCurrentRequest()->get('tokenValue');

        if (null === $tokenValue) {
            return null;
        }
        /**
         * @psalm-suppress MixedReturnStatement
         */
        return $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
    }

    public function resolve(): OrderInterface
    {
        $order = $this->getCurrentOrder();

        if (!$order instanceof OrderInterface) {
            $order = $this->cartContext->getCart();
        }

        if ($order instanceof OrderInterface) {
            return $order;
        }

        throw new NotFoundHttpException('Order was not found');
    }
}
