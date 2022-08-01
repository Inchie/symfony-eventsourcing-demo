<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Event;

use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Infrastructure\EventSourcing\EventInterface;

class UserWasCreated implements EventInterface
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

    public static function fromArray(array $values): self
    {
        return new self(UserIdentifier::fromString($values['userIdentifier']), $values['name'], $values['mail']);
    }

    public function jsonSerialize(): array
    {
        return [
            'userIdentifier' => $this->id,
            'name' => $this->name,
            'mail' => $this->mail,
        ];
    }
}
