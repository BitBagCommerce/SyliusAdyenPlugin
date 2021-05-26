<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class AdyenCredentials extends Constraint
{
    public $messageInvalidApiKey = 'bitbag_sylius_adyen_plugin.credentials.invalid_api_key';
    public $messageInvalidMerchantAccount = 'bitbag_sylius_adyen_plugin.credentials.invalid_merchant_account';

    public function validatedBy()
    {
        return 'bit_bag_sylius_adyen_plugin_credentials';
    }
}
