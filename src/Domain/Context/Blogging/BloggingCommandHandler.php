<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging;

use App\Domain\Context\Blogging\Command\CreateBlog;
use App\Domain\Context\Blogging\Command\UpdateBlog;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Store\BloggingEventStore;
use App\Domain\Helper\StreamNameHelper;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use Neos\EventSourcing\Event\DomainEvents;

class BloggingCommandHandler
{
    private $eventStore;
    private $blogRepository;

    public function __construct(
        BloggingEventStore $eventStore,
        BlogRepository $blogRepository
    )
    {
        $this->eventStore = $eventStore->create();
        $this->blogRepository = $blogRepository;
    }

    public function handleCreateBlog(CreateBlog $command)
    {
        $uuid = $this->blogRepository->nextIdentity();
        $event = new BlogWasCreated(
            $uuid,
            $command->getName(),
            $command->getAuthorIdentifier()
        );

        $stream = StreamNameHelper::fromString(
            BloggingEventStore::BLOG_STREAM_NAME,
            $uuid
        );

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleUpdateBlog(UpdateBlog $command)
    {
        $event = new BlogWasUpdated(
            $command->getId(),
            $command->getName()
        );

        $stream = StreamNameHelper::fromString(
            BloggingEventStore::BLOG_STREAM_NAME,
            $command->getId()
        );

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleStream(BlogIdentifier $blogIdentifier)
    {
        $stream = StreamNameHelper::fromString(
            BloggingEventStore::BLOG_STREAM_NAME,
            $blogIdentifier
        );

        return $this->eventStore->load($stream);
    }
}
