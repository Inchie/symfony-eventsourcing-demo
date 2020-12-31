<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Repository;

use App\Domain\Context\Blogging\Model\Blog;

interface BlogRepository
{
    public function add(Blog $blog);

    public function findAll();

    public function findByStream($stream);

    public function flush();
}
