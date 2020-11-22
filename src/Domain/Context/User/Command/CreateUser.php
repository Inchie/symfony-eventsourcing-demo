<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Command;

class CreateUser
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    public function __construct(string $name, string $mail)
    {
        $this->name = $name;
        $this->mail = $mail;
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
}
