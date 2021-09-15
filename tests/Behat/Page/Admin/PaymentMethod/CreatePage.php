<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function setAdyenPlatform(string $platform): void
    {
        $this->getDocument()->selectFieldOption('Platform', $platform);
    }

    public function setValue(string $name, $value): void
    {
        $this->getElement($name)->setValue($value);
    }

    protected function getDefinedElements(): array
    {
        return parent::getDefinedElements() + [
            'apiKey' => '#sylius_payment_method_gatewayConfig_config_apiKey',
            'merchantAccount' => '#sylius_payment_method_gatewayConfig_config_merchantAccount',
            'hmacKey' => '#sylius_payment_method_gatewayConfig_config_hmacKey',
            'clientKey' => '#sylius_payment_method_gatewayConfig_config_clientKey',
            'authUser' => '#sylius_payment_method_gatewayConfig_config_authUser',
            'authPassword' => '#sylius_payment_method_gatewayConfig_config_authPassword',
        ];
    }
}
