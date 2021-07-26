<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class AdyenCredentials extends Constraint
{
    /** @var string */
    public $messageInvalidApiKey = 'bitbag_sylius_adyen_plugin.credentials.invalid_api_key';

    /** @var string */
    public $messageInvalidMerchantAccount = 'bitbag_sylius_adyen_plugin.credentials.invalid_merchant_account';

    public function validatedBy(): string
    {
        return 'bitbag_sylius_adyen_plugin_credentials';
    }
}
