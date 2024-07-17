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
use BitBag\SyliusAdyenPlugin\Entity\AdyenTokenInterface;
use BitBag\SyliusAdyenPlugin\Factory\AdyenTokenFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTokenHandler
{
    /** @var AdyenTokenFactoryInterface */
    private $tokenFactory;

    /** @var RepositoryInterface */
    private $tokenRepository;

    public function __construct(
        AdyenTokenFactoryInterface $tokenFactory,
        RepositoryInterface $tokenRepository,
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->tokenRepository = $tokenRepository;
    }

    public function __invoke(CreateToken $createToken): AdyenTokenInterface
    {
        $token = $this->tokenFactory->create(
            $createToken->getPaymentMethod(),
            $createToken->getCustomer(),
        );

        $this->tokenRepository->add($token);

        return $token;
    }
}
