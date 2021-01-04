<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog")
 */
class Blog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $author;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Domain\Context\Blogging\Model\Comment",
     *     mappedBy="blog",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $comments;

    /**
     * @ORM\Column(type="text")
     */
    private $stream;

    private function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public static function create(
        string $name,
        string $author,
        string $stream
    )
    {
        $newUser = new self();
        $newUser->name = $name;
        $newUser->author = $author;
        $newUser->stream = $stream;

        return $newUser;
    }

    public function update(
        string $name
    )
    {
        $this->name = $name;
    }

    public function createCommentByRequest(
        string $author,
        string $comment,
        string $stream
    ): void
    {
        $newComment = Comment::create(
            $author,
            $comment,
            $stream
        );

        $newComment->setBlog($this);
        $this->comments->add($newComment);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getStream(): string
    {
        return $this->stream;
    }
}
