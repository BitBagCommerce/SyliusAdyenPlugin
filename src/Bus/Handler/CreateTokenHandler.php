<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Handler;

use BitBag\SyliusAdyenPlugin\Bus\Command\CreateToken;
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Factory\AdyenTokenFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateTokenHandler implements MessageHandlerInterface
{
    /** @var AdyenTokenFactoryInterface */
    private $tokenFactory;

    /** @var EntityManagerInterface */
    private $tokenManager;

    public function __construct(
        AdyenTokenFactoryInterface $tokenFactory,
        EntityManagerInterface $tokenManager
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(CreateToken $createToken): AdyenTokenInterface
    {
        $token = $this->tokenFactory->create($createToken->getCustomer());
        $this->tokenManager->persist($token);
        $this->tokenManager->flush();

        return $token;
    }
}
