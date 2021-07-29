<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\PaymentMethod\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function getElementValue(string $name): string
    {
        return $this->getElement($name)->getValue();
    }

    protected function getDefinedElements(): array
    {
        return parent::getDefinedElements() + [
            'apiKey' => '#sylius_payment_method_gatewayConfig_config_apiKey',
            'merchantAccount' => '#sylius_payment_method_gatewayConfig_config_merchantAccount',
            'hmacKey' => '#sylius_payment_method_gatewayConfig_config_hmacKey'
        ];
    }
}
