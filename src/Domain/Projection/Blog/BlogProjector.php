<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Neos\EventSourcing\EventListener\AppliedEventsStorage\AppliedEventsStorageInterface;
use Neos\EventSourcing\EventListener\AppliedEventsStorage\DoctrineAppliedEventsStorage;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogProjector implements ProjectorInterface, EventSubscriberInterface, AppliedEventsStorageInterface
{
    private BlogRepository $blogRepository;
    private DoctrineAppliedEventsStorage $doctrineAppliedEventsStorage;

    public function __construct(
        BlogRepository $blogRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->blogRepository = $blogRepository;

        $this->doctrineAppliedEventsStorage = new DoctrineAppliedEventsStorage(
            $entityManager->getConnection(),
            get_class($this)
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            // NOTE!!! You always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // We only use the EventSubscriber from symfony to figure out which listeners should be called.
            BlogWasCreated::class => ['whenBlogWasCreated'],
            BlogWasUpdated::class => ['whenBlogWasUpdated']
        ];
    }

    public function whenBlogWasCreated(BlogWasCreated $event, RawEvent $rawEvent)
    {
        $this->blogRepository->addByEvent($event);
    }

    public function whenBlogWasUpdated(BlogWasUpdated $event, RawEvent $rawEvent)
    {
        $this->blogRepository->updateByEvent($event);
    }

    public function reset(): void
    {
        $this->blogRepository->truncate();
    }

    public function reserveHighestAppliedEventSequenceNumber(): int
    {
        return $this->doctrineAppliedEventsStorage->reserveHighestAppliedEventSequenceNumber();
    }

    public function releaseHighestAppliedSequenceNumber(): void
    {
        $this->doctrineAppliedEventsStorage->releaseHighestAppliedSequenceNumber();
    }

    public function saveHighestAppliedSequenceNumber(int $sequenceNumber): void
    {
        $this->doctrineAppliedEventsStorage->saveHighestAppliedSequenceNumber($sequenceNumber);

        // Special things happens here
    }
}
