<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Service\NotificationReceiver;
use Adyen\Util\HmacSignature;

class SignatureValidator
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
