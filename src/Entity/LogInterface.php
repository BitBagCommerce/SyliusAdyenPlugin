<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface LogInterface extends ResourceInterface
{
    public function getId(): int;

    public function getLevel(): int;

    public function setLevel(int $level): void;

    public function getErrorCode(): int;

    public function setErrorCode(int $errorCode): void;

    public function getMessage(): string;

    public function setMessage(string $message): void;

    public function getDateTime(): \DateTime;

    public function setDateTime(\DateTime $dateTime): void;
}
