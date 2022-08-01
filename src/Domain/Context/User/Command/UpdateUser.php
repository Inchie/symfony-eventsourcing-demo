<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Command;

use App\Domain\Context\User\ValueObject\UserIdentifier;

class UpdateUser
{
    public function __construct(
        private readonly UserIdentifier $id,
        private readonly string $name,
        private readonly string $mail
    ) {
    }

    public function getId(): UserIdentifier
    {
        return $this->id;
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
