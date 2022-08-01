<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Command;

use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;

class UpdateBlog
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
}
