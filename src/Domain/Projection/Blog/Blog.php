<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

class Blog
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $author;

    /**
     * @var \ArrayIterator
     */
    private $comments;

    public static function create(
        string $id,
        string $name,
        string $author,
        \ArrayIterator $comments = null
    )
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

    /** naming because of Twig **/
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
