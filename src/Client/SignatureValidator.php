<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Service\NotificationReceiver;
use Adyen\Util\HmacSignature;

final class SignatureValidator implements SignatureValidatorInterface
{
    /** @var string */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    private function getReceiver(): NotificationReceiver
    {
        return new NotificationReceiver(
            new HmacSignature()
        );
    }

    public function isValid(array $params): bool
    {
        return $this->getReceiver()->validateHmac($params, $this->key);
    }
}
