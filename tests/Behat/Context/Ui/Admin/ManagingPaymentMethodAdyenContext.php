<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Ui\Admin;

use Adyen\AdyenException;
use Adyen\Service;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;

final class ManagingPaymentMethodAdyenContext extends MinkContext implements Context
{
    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        CreatePageInterface $createPage,
        KernelInterface $kernel
    ) {
        $this->createPage = $createPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->kernel = $kernel;
    }

    /**
     * @Given I want to create a new Adyen payment method
     *
     * @throws UnexpectedPageException
     */
    public function iWantToCreateANewAdyenPaymentMethod(): void
    {
        $this->createPage->open(['factory' => 'adyen']);
    }

    /**
     * @Given Adyen service will confirm merchantAccount :merchantAccount and apiKey :apiKey are valid
     */
    public function adyenServiceWillConfirmMerchantAccountAndApiKeyAreValid(string $merchantAccount, string $apiKey): void
    {
        $this->kernel
            ->getContainer()
            ->get('tests.bit_bag.sylius_adyen_plugin.behat.context.api_mock_client')
            ->setJsonHandler(function (Service $service, string $url, array $payload) use ($merchantAccount, $apiKey) {
                $config = $service->getClient()->getConfig();

                if ($config->getXApiKey() !== $apiKey) {
                    throw new AdyenException('', Response::HTTP_UNAUTHORIZED);
                }

                if ($payload['merchantAccount'] !== $merchantAccount) {
                    throw new AdyenException('', Response::HTTP_FORBIDDEN);
                }

                return [];
            });
    }

    /**
     * @When I specify test configuration with merchantAccount :merchantAccount and apiKey :apiKey
     */
    public function iSpecifyTestConfigurationWithMerchantAccountAndApiKey(string $merchantAccount, string $apiKey): void
    {
        $this->resolveCurrentPage()->setAdyenPlatform('test');
        $this->resolveCurrentPage()->setAdyenMerchantAccount($merchantAccount);
        $this->resolveCurrentPage()->setAdyenHmacKey('test');
        $this->resolveCurrentPage()->setApiKey($apiKey);
        $this->resolveCurrentPage()->setAuthUser('test');
        $this->resolveCurrentPage()->setAuthPassword('test');
        $this->resolveCurrentPage()->setClientKey('test');
    }

    /**
     * @return CreatePageInterface
     */
    private function resolveCurrentPage()
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);
    }
}
