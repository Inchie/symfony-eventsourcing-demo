<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog\Repository;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Projection\Blog\Blog;
use App\Domain\Projection\Blog\BlogIdentifier;

interface BlogRepository
{
    public function nextIdentity(): BlogIdentifier;

    public function findAll();

    public function findById(BlogIdentifier $id): Blog;

    public function addByEvent(BlogWasCreated $event);

    public function updateByEvent(BlogWasUpdated $event);

    public function truncate();
}
