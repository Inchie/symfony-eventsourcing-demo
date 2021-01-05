<?php

declare(strict_types=1);

namespace App\Domain\Projection\Comment\Repository;

use App\Domain\Context\Commenting\Event\CommentWasCreated;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\Comment\CommentIdentifier;

interface CommentRepository
{
    public function nextIdentity(): CommentIdentifier;

    public function findAll(): \ArrayIterator;

    public function findByBlog(BlogIdentifier $blogIdentifier): \ArrayIterator;

    public function addByEvent(CommentWasCreated $event);
}
