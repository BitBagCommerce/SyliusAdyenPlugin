<?php declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Entity;

/** @psalm-suppress MissingConstructor */
class Log implements LogInterface
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $level;

    /** @var int */
    protected $errorCode;

    /** @var string */
    protected $message;

    /** @var \DateTime */
    protected $dateTime;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function setErrorCode(int $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }
}
