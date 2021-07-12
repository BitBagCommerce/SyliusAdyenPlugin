<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller\Shop;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\AdyenTokenRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RemoveStoredTokenAction
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var AdyenTokenRepositoryInterface */
    private $adyenTokenRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(
        TokenStorageInterface $storage,
        AdyenTokenRepositoryInterface $adyenTokenRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        AdyenClientProvider $adyenClientProvider
    ) {
        $this->tokenStorage = $storage;
        $this->adyenTokenRepository = $adyenTokenRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->adyenClientProvider = $adyenClientProvider;
    }

    private function getUser(): ShopUserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            throw new \InvalidArgumentException();
        }

        $user = $token->getUser();
        if (!$user instanceof ShopUserInterface) {
            throw new \InvalidArgumentException();
        }

        return $user;
    }

    public function __invoke(string $code, string $paymentReference, Request $request): Response
    {
        /**
         * @var ?CustomerInterface $customer
         */
        $customer = $this->getUser()->getCustomer();
        if ($customer === null) {
            throw new \InvalidArgumentException();
        }

        $paymentMethod = $this->paymentMethodRepository->getOneForAdyenAndCode($code);

        $token = $this->adyenTokenRepository->findOneByPaymentMethodAndCustomer($paymentMethod, $customer);
        if ($token === null) {
            throw new \InvalidArgumentException();
        }

        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);

        $client->removeStoredToken($paymentReference, (string) $token->getIdentifier());

        return new Response();
    }
}
