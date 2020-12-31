<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Context\Blogging\Model\Blog", inversedBy="comments")
     */
    private $blog;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="text")
     */
    private $stream;

    public static function create(
        string $author,
        string $comment,
        string $stream
    ): self
    {
        $newComment = new self();
        $newComment->author = $author;
        $newComment->comment = $comment;
        $newComment->stream = $stream;

        return $newComment;
    }

    public function setBlog($blog)
    {
        $this->blog = $blog;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getComment()
    {
        return $this->comment;
    }
}
