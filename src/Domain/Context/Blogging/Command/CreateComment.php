<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Command;

use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;

class CreateComment
{
    /**
     * @param UserIdentifier $authorIdentifier
     * @param string $comment
     */
    public function __construct(
        private readonly BlogIdentifier $blogIdentifier,
        private readonly UserIdentifier $authorIdentifier,
        private readonly string $comment
    ) {
    }

    public function getBlogIdentifier(): BlogIdentifier
    {
        return $this->blogIdentifier;
    }

    public function getAuthorIdentifier(): UserIdentifier
    {
        return $this->authorIdentifier;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
