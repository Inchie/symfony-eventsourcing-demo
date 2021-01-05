<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog;

use App\Domain\Projection\Blog\Repository\BlogRepository;
use App\Domain\Projection\Comment\Repository\CommentRepository;

class BlogFinder
{
    private $blogRepository;
    private $commentRepository;

    public function __construct(
        BlogRepository $blogRepository,
        CommentRepository $commentRepository
    )
    {
        $this->blogRepository = $blogRepository;
        $this->commentRepository = $commentRepository;
    }

    public function execute(BlogIdentifier $blogIdentifier)
    {
        $comments = $this->commentRepository->findByBlog(
            $blogIdentifier
        );

        $blog = $this->blogRepository->findById($blogIdentifier);

        if ($comments->count() !== 0) {
            $blog->addComments($comments);
        }
        return $blog;
    }
}
