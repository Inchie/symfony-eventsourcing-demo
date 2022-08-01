<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\CommentWasCreated;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use App\Domain\Projection\Blog\Repository\CommentRepository;
use App\Domain\Projection\ProjectionTrait;
use App\Infrastructure\EventSourcing\EventNormalizer;
use App\Infrastructure\EventSourcing\Projection\ProjectionInterface;
use Doctrine\DBAL\Connection;
use Neos\EventStore\Model\Event\SequenceNumber;

class BlogProjection implements ProjectionInterface
{
    use ProjectionTrait;

    public function __construct(
        private readonly Connection $connection,
        private readonly BlogRepository $blogRepository,
        private readonly EventNormalizer $eventNormalizer,
        private readonly CommentRepository $commentRepository
    ) {
    }

    public function whenBlogWasCreated(BlogWasCreated $event)
    {
        $this->blogRepository->addByEvent($event);
    }

    public function whenBlogWasUpdated(BlogWasUpdated $event)
    {
        $this->blogRepository->updateByEvent($event);
    }

    public function whenCommentWasCreated(CommentWasCreated $event)
    {
        $this->commentRepository->addByEvent($event);
    }

    public function reset(): void
    {
        $this->blogRepository->truncate();
        $this->commentRepository->truncate();
        $this->getCheckpointStorage()
            ->updateAndReleaseLock(SequenceNumber::fromInteger(0));
    }

    public static function getCheckpointSubscriberId(): string
    {
        return 'Blog';
    }
}
