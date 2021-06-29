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

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;

final class ManagingPaymentMethodAdyenContext implements Context
{
    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var CreatePageInterface */
    private $createPage;

    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        CreatePageInterface $createPage
    ) {
        $this->createPage = $createPage;
        $this->currentPageResolver = $currentPageResolver;
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
     * @When I configure it with test Adyen credentials
     */
    public function iConfigureItWithTestAdyenCredentials(): void
    {
        $this->resolveCurrentPage()->setAdyenPlatform('test');
        $this->resolveCurrentPage()->setAdyenMerchantAccount('test');
        $this->resolveCurrentPage()->setAdyenHmacKey('test');
        $this->resolveCurrentPage()->setAuthUser('test');
        $this->resolveCurrentPage()->setAuthPassword('test');
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
