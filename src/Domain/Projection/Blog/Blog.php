<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

class Blog
{
    private ?string $id = null;

    private ?string $name = null;

    private ?string $author = null;

    private ?\ArrayIterator $comments = null;

    public static function create(string $id, string $name, string $author, \ArrayIterator $comments = null): self
    {
        if ($comments === null) {
            $comments = new \ArrayIterator();
        }

        $newBlog = new self();
        $newBlog->id = $id;
        $newBlog->name = $name;
        $newBlog->author = $author;
        $newBlog->comments = $comments;

        return $newBlog;
    }

    /**
     * naming because of Twig *
     */
    public function addComments(\ArrayIterator $comments)
    {
        $this->comments = $comments;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getComments(): array
    {
        return $this->comments->getArrayCopy();
    }
}
