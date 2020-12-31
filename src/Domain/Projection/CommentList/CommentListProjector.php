<?php

declare(strict_types=1);

namespace App\Domain\Projection\CommentList;

use App\Domain\Context\Blogging\Model\Blog;
use App\Domain\Context\Blogging\Repository\BlogRepository;
use App\Domain\Context\Commenting\Event\CommentWasCreated;
use Doctrine\DBAL\Connection;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentListProjector implements ProjectorInterface, EventSubscriberInterface
{
    public const TABLE_NAME = 'comment_list';

    private $blogRepository;

    public function __construct(
        BlogRepository $blogRepository
    )
    {
        $this->blogRepository = $blogRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            // NOTE!!! you always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // we only use the EventSubscriber from symfony to figure out which listeners should be called.
            CommentWasCreated::class => ['whenCommentWasCreated']
        ];
    }

    /**
     * @var Connection
     */
    private $connection;

    public function whenCommentWasCreated(CommentWasCreated $event, RawEvent $rawEvent)
    {
        $blog = $this->blogRepository->findByStream(
            $event->getBlogIdentifier()->jsonSerialize()
        );
        /* @var $blog Blog */
        $blog->createCommentByRequest(
            $event->getAuthorIdentifier()->jsonSerialize(),
            $event->getComment(),
            $event->getStreamName()
        );

        $this->blogRepository->add($blog);
        $this->blogRepository->flush();

        //dump("Hier würde ich in die tabelle schreiben");
        /*$this->connection->insert(self::TABLE_NAME, [
            'author' => $event->getAuthorIdentifier(),
            'comment' => $event->getComment()
        ]); // AUCH LEGITIM, hier ORM zu verwenden
        */
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
        // TODO: hier würde ich die Tabelle leeren
    }
}
