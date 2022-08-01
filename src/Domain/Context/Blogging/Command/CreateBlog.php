<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Command;

use App\Domain\Context\User\ValueObject\UserIdentifier;

class CreateBlog
{
    public function __construct(
        private readonly string $name,
        private readonly UserIdentifier $authorIdentifier
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthorIdentifier(): UserIdentifier
    {
        return $this->authorIdentifier;
    }
}
