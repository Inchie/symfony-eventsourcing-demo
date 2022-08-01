<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Event;

use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Infrastructure\EventSourcing\EventInterface;

class BlogWasUpdated implements EventInterface
{
    public function __construct(
        private readonly BlogIdentifier $id,
        private readonly string $name
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

    public static function fromArray(array $values): EventInterface
    {
        return new self(BlogIdentifier::fromString($values['blogIdentifier']), $values['name']);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'blogIdentifier' => $this->id,
            'name' => $this->name,
        ];
    }
}
