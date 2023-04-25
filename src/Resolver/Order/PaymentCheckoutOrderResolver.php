<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PaymentCheckoutOrderResolver implements PaymentCheckoutOrderResolverInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var CartContextInterface */
    private $cartContext;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        RequestStack $requestStack,
        CartContextInterface $cartContext,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->requestStack = $requestStack;
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
    }

    private function getCurrentRequest(): Request
    {
        $result = $this->requestStack->getCurrentRequest();
        if (null === $result) {
            throw new BadRequestException('No request provided');
        }

        return $result;
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    private function getCurrentOrder(): ?OrderInterface
    {
        /**
         * @var string|null $tokenValue
         * @psalm-suppress InternalMethod
         */
        $tokenValue = $this->getCurrentRequest()->get('tokenValue');

        if (null === $tokenValue) {
            return null;
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);

        return $order;
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
