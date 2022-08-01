<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging;

use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Context\Blogging\Command\CreateComment;
use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\CommentWasCreated;
use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Infrastructure\EventSourcing\AppEventStore;
use App\Infrastructure\EventSourcing\CommandResult;
use App\Infrastructure\EventSourcing\Events;
use Neos\EventStore\Model\EventStream\EventStreamInterface;
use Ramsey\Uuid\Uuid;

class BloggingCommandHandler
{
    public function __construct(
        private readonly AppEventStore $eventStore
    ) {
    }

    public function handleCreateBlog(CreateBlog $command): CommandResult
    {
        $blogIdentifier = BlogIdentifier::fromString(Uuid::uuid4()->toString());
        $event = new BlogWasCreated($blogIdentifier, $command->getName(), $command->getAuthorIdentifier());

        $commandResult = $this->eventStore->commit($blogIdentifier, Events::with($event));
        $commandResult->block();

        return $commandResult;
    }

    public function handleUpdateBlog(UpdateBlog $command): CommandResult
    {
        $event = new BlogWasUpdated($command->getId(), $command->getName());

        $commandResult = $this->eventStore->commit($command->getId(), Events::with($event));
        $commandResult->block();

        return $commandResult;
    }

    public function handleCreateComment(CreateComment $command): CommandResult
    {
        $event = new CommentWasCreated(
            $command->getBlogIdentifier(),
            $command->getAuthorIdentifier(),
            $command->getComment()
        );
        return $this->eventStore->commit($command->getBlogIdentifier(), Events::with($event));
    }

    public function handleStream(BlogIdentifier $blogIdentifier): EventStreamInterface
    {
        return $this->eventStore->load($blogIdentifier);
    }
}
