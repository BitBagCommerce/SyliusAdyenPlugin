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
        $token = $this->tokenFactory->create(
            $createToken->getPaymentMethod(),
            $createToken->getCustomer()
        );
        $this->tokenManager->persist($token);
        $this->tokenManager->flush();

        return $token;
    }
}
