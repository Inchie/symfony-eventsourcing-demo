<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Event;

use App\Domain\ValueObject\UserIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class BlogWasCreated implements DomainEventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var UserIdentifier
     */
    private $author;

    /**
     * @var string
     */
    private $streamName;

    /**
     * BlogWasCreated constructor.
     * @param string $name
     * @param UserIdentifier $author
     * @param string $streamName
     */
    public function __construct(
        string $name,
        UserIdentifier $author,
        string $streamName
    )
    {
        $this->name = $name;
        $this->author = $author;
        $this->streamName = $streamName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return UserIdentifier
     */
    public function getAuthor(): UserIdentifier
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }
}
