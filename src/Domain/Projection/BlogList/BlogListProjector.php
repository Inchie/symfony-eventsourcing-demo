<?php

declare(strict_types=1);

namespace App\Domain\Projection\BlogList;

use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Model\Blog;
use App\Domain\Context\Blogging\Repository\BlogRepository;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogListProjector implements ProjectorInterface, EventSubscriberInterface
{
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            // NOTE!!! you always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // we only use the EventSubscriber from symfony to figure out which listeners should be called.
            BlogWasCreated::class => ['whenBlogWasCreated'],
            BlogWasUpdated::class => ['whenBlogWasUpdated']
        ];
    }

    public function whenBlogWasCreated(BlogWasCreated $event, RawEvent $rawEvent)
    {
        $newBlog = Blog::create(
            $event->getName(),
            $event->getAuthor()->jsonSerialize(),
            $event->getStreamName()
        );
        $this->blogRepository->add($newBlog);
        $this->blogRepository->flush();
    }

    public function whenBlogWasUpdated(BlogWasUpdated $event, RawEvent $rawEvent)
    {
        $blog = $this->blogRepository->findByStream($event->getStreamName());
        $blog->update(
            $event->getName()
        );

        $this->blogRepository->add($blog);
        $this->blogRepository->flush();
    }

    public function reset(): void
    {
        // TODO: hier würde ich die Tabelle leeren
    }
}
