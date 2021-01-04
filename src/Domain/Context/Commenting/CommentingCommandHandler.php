<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting;

use App\Domain\Context\Blogging\Store\BloggingEventStore;
use App\Domain\Context\Commenting\Command\CreateComment;
use App\Domain\Context\Commenting\Event\CommentWasCreated;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\EventStore;
use Neos\EventSourcing\EventStore\StreamName;

class CommentingCommandHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(BloggingEventStore $eventStore)
    {
        $this->eventStore = $eventStore->create();
    }

    public function handleCreateComment(CreateComment $command)
    {
        $this->requireUserToExist($command->getAuthorIdentifier());

        $streamName = sprintf(
            'Comment:%s',
            uniqid()
        );

        $event = new CommentWasCreated(
            $command->getBlogIdentifier(),
            $command->getAuthorIdentifier(),
            $command->getComment(),
            $streamName
        );

        $stream = StreamName::fromString($streamName);

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    private function requireUserToExist($getAuthorIdentifier)
    {
        // read a model, throw exception if it does not exist.
        // This would be a soft constraint check.

        // or, build up an Aggregate and ask it; this would be a hard constraint check.
        // If in doubt, use soft constraints :)
    }
}
