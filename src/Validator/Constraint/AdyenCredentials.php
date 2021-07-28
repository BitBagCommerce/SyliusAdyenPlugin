<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
