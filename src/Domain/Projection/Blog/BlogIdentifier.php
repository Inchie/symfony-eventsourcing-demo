<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use Ramsey\Uuid\UuidInterface;

final class BlogIdentifier implements \JsonSerializable
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUuid(UuidInterface $id): self
    {
        return new self($id->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function jsonSerialize(): string
    {
        return $this->id;
    }

    public function toString(): string
    {
        return $this->id;
    }
}
