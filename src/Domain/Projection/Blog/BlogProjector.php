<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogProjector implements ProjectorInterface, EventSubscriberInterface
{
    private $blogRepository;

    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
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
        throw new \Exception('Reset is not supported at the moment');
    }
}
