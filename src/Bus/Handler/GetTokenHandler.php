<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateToken;
use BitBag\SyliusAdyenPlugin\Bus\Dispatcher;
use BitBag\SyliusAdyenPlugin\Bus\Query\GetToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Repository\AdyenTokenRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

class GetTokenHandler implements MessageHandlerInterface
{
    /** @var AdyenTokenRepositoryInterface */
    private $adyenTokenRepository;

    /** @var Dispatcher */
    private $dispatcher;

    public function __construct(
        AdyenTokenRepositoryInterface $adyenTokenRepository,
        Dispatcher $dispatcher
    ) {
        $this->adyenTokenRepository = $adyenTokenRepository;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(GetToken $getTokenQuery): AdyenTokenInterface
    {
        $customer = $getTokenQuery->getOrder()->getCustomer();
        if ($customer === null) {
            throw new \InvalidArgumentException(
                sprintf('An order %d has no customer associated', $getTokenQuery->getOrder()->getId())
            );
        }

        Assert::isInstanceOf($customer, CustomerInterface::class);

        $token = $this->adyenTokenRepository->findOneByCustomer($customer);
        if ($token !== null) {
            return $token;
        }

        return $this->dispatcher->dispatch(
            new CreateToken($customer)
        );
    }
}
