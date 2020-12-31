<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting\Event;

use App\Domain\ValueObject\BlogIdentifier;
use App\Domain\ValueObject\UserIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class CommentWasCreated implements DomainEventInterface
{
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

    /**
     * @var string
     */
    private $streamName;

    public function __construct(
        BlogIdentifier $blogIdentifier,
        UserIdentifier $authorIdentifier,
        string $comment,
        string $streamName
    ) {
        $this->blogIdentifier = $blogIdentifier;
        $this->authorIdentifier = $authorIdentifier;
        $this->comment = $comment;
        $this->streamName = $streamName;
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

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }
}
