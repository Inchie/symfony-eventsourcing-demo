<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting;

use App\Domain\Context\Commenting\Command\CreateComment;
use App\Domain\Context\Commenting\Event\CommentWasCreated;
use App\Domain\Context\Commenting\Store\CommentingEventStore;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\EventStore;
use Neos\EventSourcing\EventStore\StreamName;

class CommentingCommandHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * CommentingCommandHandler constructor.
     * @param CommentingEventStore $eventStore
     */
    public function __construct(CommentingEventStore $eventStore)
    {
        $this->eventStore = $eventStore->create();

        // TODO: also inject finders from projections which you want :)
    }

    public function handleCreateComment(CreateComment $command)
    {
        $this->requireUserToExist($command->getAuthorIdentifier());

        $event = new CommentWasCreated(
            $command->getAuthorIdentifier(),
            $command->getComment()
        );

        $stream = StreamName::fromString("foo");

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
