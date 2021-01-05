<?php

declare(strict_types=1);

namespace App\Domain\Projection\Comment\Infrastructure;

use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Commenting\Event\CommentWasCreated;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\Comment\Comment;
use App\Domain\Projection\Comment\CommentIdentifier;
use App\Domain\Projection\Comment\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class DoctrineCommentRepository implements CommentRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function nextIdentity(): CommentIdentifier
    {
        return CommentIdentifier::fromUuid(
            Uuid::uuid4()
        );
    }

    public function findAll(): \ArrayIterator
    {
        $query = "
            SELECT
                comment.author,
                comment.comment,
                blog.name As blogName,
                blog.id AS blogIdentifier   
            FROM `comment`   
            INNER JOIN blog ON 
                blog.id = comment.blog_id
        ";

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                []
            )
            ->fetchAll();

        $blogs = new \ArrayIterator();
        foreach ($records as $row) {
            $blogs->append(
                Comment::create(
                    $row['author'],
                    $row['comment'],
                    $row['blogName'],
                    $row['blogIdentifier']
                )
            );
        }
        return $blogs;
    }

    public function findByBlog(BlogIdentifier $blogIdentifier): \ArrayIterator
    {
        $query = "
            SELECT 
                comment.comment As comment,
                user.name As author
            FROM `comment`   
            INNER JOIN user ON 
                user.id = comment.author
            WHERE blog_id = :blogId 
        ";

        $queryParams = [
            'blogId' => $blogIdentifier->jsonSerialize()
        ];

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            )
            ->fetchAll();

        $comments = new \ArrayIterator();
        foreach ($records as $row) {
            $comments->append(
                Comment::create(
                    $row['author'],
                    $row['comment']
                )
            );
        }
        return $comments;
    }

    public function addByEvent(CommentWasCreated $event)
    {
        $query = "
            INSERT INTO `comment`
            SET
                `id` = :id,
                `blog_id` = :blog,
                `author` = :author,
                `comment` = :comment
            ";

        // set query params
        $queryParams = [
            'id' => $event->getId()->jsonSerialize(),
            'blog' => $event->getBlogIdentifier()->jsonSerialize(),
            'author' => $event->getAuthorIdentifier()->jsonSerialize(),
            'comment' => $event->getComment()
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            );
    }
}
