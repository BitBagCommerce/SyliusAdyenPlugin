<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Notification\Struct;

class NotificationItemData
{
    /** @var ?array */
    public $additionalData;

    /** @var ?Amount */
    public $amount;

    /** @var ?string */
    public $eventCode;

    /** @var ?string */
    public $eventDate;

    /** @var ?string */
    public $merchantAccountCode;

    /** @var ?string */
    public $merchantReference;

    /** @var ?string */
    public $paymentMethod;

    /** @var ?bool */
    public $success;

    /** @var ?string */
    public $pspReference;

    /** @var ?string */
    public $originalReference;

    /** @var ?array */
    public $operations;

    /** @var ?string */
    public $paymentCode;

    /** @var ?string */
    public $reason;
}
