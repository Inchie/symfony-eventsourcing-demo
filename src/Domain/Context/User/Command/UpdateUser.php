<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Command;

class UpdateUser
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $stream;

    public function __construct(
        string $name,
        string $mail,
        string $stream
    )
    {
        $this->name = $name;
        $this->mail = $mail;
        $this->stream = $stream;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getStream(): string
    {
        return $this->stream;
    }
}
