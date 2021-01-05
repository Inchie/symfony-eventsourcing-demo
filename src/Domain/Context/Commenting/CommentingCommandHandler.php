<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting;

use App\Domain\Context\Blogging\Store\BloggingEventStore;
use App\Domain\Context\Commenting\Command\CreateComment;
use App\Domain\Context\Commenting\Event\CommentWasCreated;
use App\Domain\Helper\StreamNameHelper;
use App\Domain\Projection\Comment\Repository\CommentRepository;
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
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct(
        BloggingEventStore $eventStore,
        CommentRepository $commentRepository
    )
    {
        $this->eventStore = $eventStore->create();
        $this->commentRepository = $commentRepository;
    }

    public function handleCreateComment(CreateComment $command)
    {
        $this->requireUserToExist($command->getAuthorIdentifier());

        $uuid = $this->commentRepository->nextIdentity();
        $event = new CommentWasCreated(
            $uuid,
            $command->getBlogIdentifier(),
            $command->getAuthorIdentifier(),
            $command->getComment()
        );

        $stream = StreamNameHelper::fromString(
            BloggingEventStore::COMMENT_STREAM_NAME,
            $uuid
        );

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
