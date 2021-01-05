<?php

declare(strict_types=1);

namespace App\Domain\Projection\Blog\Infrastructure;

use App\Domain\Context\Blog\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Projection\Blog\Blog;
use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\Blog\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Ramsey\Uuid\Uuid;

class DoctrineBlogRepository implements BlogRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll()
    {
        $query = "
            SELECT 
                id,
                name,
                author   
            FROM `blog`   
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
                Blog::create(
                    $row['id'],
                    $row['name'],
                    $row['author']
                )
            );
        }
        return $blogs;
    }

    public function findById(BlogIdentifier $blogIdentifier): Blog
    {
        $query = "
            SELECT 
                   blog.id As blogId,
                   blog.name As blogName,
                   blog.author As blogAuthor
            FROM `blog`   
            WHERE blog.id = :id 
        ";

        $queryParams = [
            'id' => $blogIdentifier->jsonSerialize()
        ];

        $record = $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            )
            ->fetch();

        if ($record === false) {
            throw new \Exception('Could not find the blog with given id');
        }

        return Blog::create(
            $record['blogId'],
            $record['blogName'],
            $record['blogAuthor']
        );
    }

    public function nextIdentity(): BlogIdentifier
    {
        return BlogIdentifier::fromUuid(
            Uuid::uuid4()
        );
    }

    public function addByEvent(BlogWasCreated $event)
    {
        $query = "
            INSERT INTO `blog`
            SET
                `id` = :id,
                `name` = :name,
                `author` = :author
            ";

        // set query params
        $queryParams = [
            'id' => $event->getId()->jsonSerialize(),
            'name' => $event->getName(),
            'author' => $event->getAuthor()->jsonSerialize()
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            );
    }

    public function updateByEvent(BlogWasUpdated $event)
    {
        $query = "
            UPDATE `blog`
            SET
                `name` = :name
            where id = :id
        ";

        // set query params
        $queryParams = [
            'id' => $event->getId()->jsonSerialize(),
            'name' => $event->getName()
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            );
    }

    public function truncate()
    {
        $query = "
            TRUNCATE TABLE blog;
        ";


        $this->entityManager
            ->getConnection()
            ->exec($query);
    }
}
