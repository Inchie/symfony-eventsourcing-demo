<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

class Comment
{
    private ?string $author = null;

    private ?string $comment = null;

    private ?string $blogName = null;

    private ?string $blogIdentifier = null;

    public static function create(
        string $author,
        string $comment,
        string $blogName = '',
        string $blogIdentifier = ''
    ): self {
        $newComment = new self();
        $newComment->author = $author;
        $newComment->comment = $comment;
        $newComment->blogName = $blogName;
        $newComment->blogIdentifier = $blogIdentifier;
        return $newComment;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getBlogName(): string
    {
        return $this->blogName;
    }

    public function getBlogIdentifier(): string
    {
        return $this->blogIdentifier;
    }
}
