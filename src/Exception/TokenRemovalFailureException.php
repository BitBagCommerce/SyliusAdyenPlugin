<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Exception;

class TokenRemovalFailureException extends \InvalidArgumentException
{
    public static function forAnonymous(): self
    {
        return new self('Cannot delete token for anonymous user');
    }

    public static function forNonExistingToken(): self
    {
        return new self('Cannot delete non-existing token');
    }
}
