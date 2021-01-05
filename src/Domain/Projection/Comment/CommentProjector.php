<?php

declare(strict_types=1);

namespace App\Domain\Projection\Comment;

use App\Domain\Context\Commenting\Event\CommentWasCreated;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use App\Domain\Projection\Comment\Repository\CommentRepository;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentProjector implements ProjectorInterface, EventSubscriberInterface
{
    public const TABLE_NAME = 'comment_list';

    private $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // NOTE!!! You always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // We only use the EventSubscriber from symfony to figure out which listeners should be called.
            CommentWasCreated::class => ['whenCommentWasCreated']
        ];
    }

    public function whenCommentWasCreated(
        CommentWasCreated $event,
        RawEvent $rawEvent
    ): void
    {
        $this->commentRepository->addByEvent($event);
    }

    /*
        public function whenCommentWasRemoved(CommentWasRemoved $event, RawEvent $rawEvent) {
            $this->connection->delete(self::TABLE_NAME, [
                'author' => $event->getAuthorIdentifier(),
            ]);
        }
    */

    public function reset(): void
    {
        $this->commentRepository->truncate();
    }
}
