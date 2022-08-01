<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use App\Domain\Projection\Blog\Repository\BlogRepository;
use App\Domain\Projection\Blog\Repository\CommentRepository;

class BlogFinder
{
    public function __construct(
        private readonly BlogRepository $blogRepository,
        private readonly CommentRepository $commentRepository
    ) {
    }

    public function execute(\App\Domain\Context\Blogging\ValueObject\BlogIdentifier $blogIdentifier)
    {
        $comments = $this->commentRepository->findByBlog($blogIdentifier);

        $blog = $this->blogRepository->findById($blogIdentifier);

        if ($comments->count() !== 0) {
            $blog->addComments($comments);
        }
        return $blog;
    }
}
