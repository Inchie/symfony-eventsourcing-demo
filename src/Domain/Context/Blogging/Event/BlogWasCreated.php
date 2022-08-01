<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Event;

use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Infrastructure\EventSourcing\EventInterface;

class BlogWasCreated implements EventInterface
{
    public function __construct(
        private readonly BlogIdentifier $id,
        private readonly string $name,
        private readonly UserIdentifier $author
    ) {
    }

    public function getId(): BlogIdentifier
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): UserIdentifier
    {
        return $this->author;
    }

    public static function fromArray(array $values): EventInterface
    {
        return new self(
            BlogIdentifier::fromString($values['blogIdentifier']),
            $values['name'],
            UserIdentifier::fromString($values['author'])
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'blogIdentifier' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
        ];
    }
}
