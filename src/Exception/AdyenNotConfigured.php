<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Exception;

class AdyenNotConfigured extends \InvalidArgumentException
{
    public function __construct(string $code)
    {
        $message = sprintf('Adyen for "%s" code is not configured', $code);
        parent::__construct($message);
    }
}
