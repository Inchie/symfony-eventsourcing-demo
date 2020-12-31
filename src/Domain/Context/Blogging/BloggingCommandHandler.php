<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging;

use App\Domain\Context\Blog\Command\UpdateBlog;
use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Store\BloggingEventStore;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\StreamName;

class BloggingCommandHandler
{
    private $eventStore;

    public function __construct(BloggingEventStore $eventStore)
    {
        $this->eventStore = $eventStore->create();
    }

    public function handleCreateBlog(CreateBlog $command)
    {
        $streamName = sprintf(
            'Blog:%s',
            uniqid()
        );

        $event = new BlogWasCreated(
            $command->getName(),
            $command->getAuthorIdentifier(),
            $streamName
        );

        $stream = StreamName::fromString($streamName);

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleUpdateBlog(UpdateBlog $command)
    {
        $streamName = $command->getStream();

        $event = new BlogWasUpdated(
            $command->getName(),
            $streamName
        );

        $stream = StreamName::fromString($streamName);

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleStream($streamName)
    {
        return $this->eventStore->load(StreamName::fromString($streamName));
    }
}
