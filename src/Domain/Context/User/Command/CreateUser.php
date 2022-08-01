<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Command;

class CreateUser
{
    public function __construct(
        private readonly string $name,
        private readonly string $mail
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMail(): string
    {
        return $this->mail;
    }
}
