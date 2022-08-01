<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog\Infrastructure;

use App\Domain\Context\Blogging\Event\CommentWasCreated;
use App\Domain\Projection\Blog\Comment;
use App\Domain\Projection\Blog\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class DoctrineCommentRepository implements CommentRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findAll(): \ArrayIterator
    {
        $query = '
            SELECT
                comment.author,
                comment.comment,
                blog.name As blogName,
                blog.id AS blogIdentifier   
            FROM `comment`   
            INNER JOIN blog ON 
                blog.id = comment.blog_id
        ';

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery($query, [])
            ->fetchAll();

        $blogs = new \ArrayIterator();
        foreach ($records as $row) {
            $blogs->append(
                Comment::create($row['author'], $row['comment'], $row['blogName'], $row['blogIdentifier'])
            );
        }
        return $blogs;
    }

    public function findByBlog(\App\Domain\Context\Blogging\ValueObject\BlogIdentifier $blogIdentifier): \ArrayIterator
    {
        $query = '
            SELECT 
                comment.comment As comment,
                user.name As author
            FROM `comment`   
            INNER JOIN user ON 
                user.id = comment.author
            WHERE blog_id = :blogId 
        ';

        $queryParams = [
            'blogId' => $blogIdentifier->jsonSerialize(),
        ];

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams)
            ->fetchAll();

        $comments = new \ArrayIterator();
        foreach ($records as $row) {
            $comments->append(Comment::create($row['author'], $row['comment']));
        }
        return $comments;
    }

    public function addByEvent(CommentWasCreated $event)
    {
        $query = '
            INSERT INTO `comment`
            SET
                `id` = :id,
                `blog_id` = :blog,
                `author` = :author,
                `comment` = :comment
            ';

        // set query params
        $queryParams = [
            'id' => Uuid::uuid4()->toString(),
            'blog' => $event->getBlogIdentifier()
                ->jsonSerialize(),
            'author' => $event->getUserIdentifier()
                ->jsonSerialize(),
            'comment' => $event->getComment(),
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams);
    }

    public function truncate()
    {
        $query = '
            TRUNCATE TABLE comment;
        ';

        $this->entityManager
            ->getConnection()
            ->exec($query);
    }
}
