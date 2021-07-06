<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function setAdyenPlatform(string $environment): void;

    public function setAdyenMerchantAccount(string $merchantAccount): void;

    public function setAdyenHmacKey(string $hmacKey): void;

    public function setAuthUser(string $authUser): void;

    public function setAuthPassword(string $authPassword): void;

    public function setApiKey(string $apiKey): void;

    public function setClientKey(string $string);
}
