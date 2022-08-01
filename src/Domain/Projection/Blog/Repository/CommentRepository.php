<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog\Repository;

use App\Domain\Context\Blogging\Event\CommentWasCreated;

interface CommentRepository
{
    public function findAll(): \ArrayIterator;

    public function findByBlog(\App\Domain\Context\Blogging\ValueObject\BlogIdentifier $blogIdentifier): \ArrayIterator;

    public function addByEvent(CommentWasCreated $event);

    public function truncate();
}
