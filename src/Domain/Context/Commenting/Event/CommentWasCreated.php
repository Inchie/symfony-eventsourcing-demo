<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting\Event;

use App\Domain\Projection\Comment\CommentIdentifier;
use App\Domain\ValueObject\BlogIdentifier;
use App\Domain\ValueObject\UserIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class CommentWasCreated implements DomainEventInterface
{
    /**
     * @var CommentIdentifier
     */
    private $id;

    /**
     * @var BlogIdentifier
     */
    private $blogIdentifier;

    /**
     * @var UserIdentifier
     */
    private $authorIdentifier;

    /**
     * @var string
     */
    private $comment;

    public function __construct(
        CommentIdentifier $id,
        BlogIdentifier $blogIdentifier,
        UserIdentifier $authorIdentifier,
        string $comment
    ) {
        $this->id = $id;
        $this->blogIdentifier = $blogIdentifier;
        $this->authorIdentifier = $authorIdentifier;
        $this->comment = $comment;
    }

    public function getId(): CommentIdentifier
    {
        return $this->id;
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
