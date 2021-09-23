<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateToken;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Exception\OrderWithoutCustomerException;
use BitBag\SyliusAdyenPlugin\Repository\AdyenTokenRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

final class GetTokenHandler implements MessageHandlerInterface
{
    /** @var AdyenTokenRepositoryInterface */
    private $adyenTokenRepository;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        AdyenTokenRepositoryInterface $adyenTokenRepository,
        Dispatcher $dispatcher,
        TokenStorageInterface $tokenStorage
    ) {
        $this->adyenTokenRepository = $adyenTokenRepository;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    private function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface ? $user : null;
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function __invoke(GetToken $getTokenQuery): ?AdyenTokenInterface
    {
        if ($this->getUser() === null) {
            return null;
        }

        $customer = $getTokenQuery->getOrder()->getCustomer();
        if ($customer === null) {
            throw new OrderWithoutCustomerException($getTokenQuery->getOrder());
        }

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            'Customer doesn\'t implement a core CustomerInterface'
        );

        $token = $this->adyenTokenRepository->findOneByPaymentMethodAndCustomer(
            $getTokenQuery->getPaymentMethod(),
            $customer
        );

        if ($token !== null) {
            return $token;
        }

        return $this->dispatcher->dispatch(
            new CreateToken($getTokenQuery->getPaymentMethod(), $customer)
        );
    }
}
