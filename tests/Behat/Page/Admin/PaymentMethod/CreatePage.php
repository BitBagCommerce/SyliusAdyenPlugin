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

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function setAdyenPlatform(string $platform): void
    {
        $this->getDocument()->selectFieldOption('Platform', $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenMerchantAccount(string $merchantAccount): void
    {
        $this->getDocument()->fillField('Merchant account', $merchantAccount);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenHmacKey(string $hmacKey): void
    {
        $this->getDocument()->fillField('HMAC Key', $hmacKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthUser(string $authUser): void
    {
        $this->getDocument()->fillField('authUser', $authUser);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthPassword(string $authPassword): void
    {
        $this->getDocument()->fillField('authPassword', $authPassword);
    }
}
