<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Infrastructure;

use App\Domain\Context\Blogging\Model\Blog;
use App\Domain\Context\Blogging\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineBlogRepository implements BlogRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Blog $blog)
    {
        $this->entityManager->persist($blog);
    }

    public function findAll()
    {
        return $this->entityManager
            ->getRepository(Blog::class)
            ->findAll();
    }

    public function findByStream($stream)
    {
        return $this->entityManager
            ->getRepository(Blog::class)
            ->findOneBy(
                [
                    'stream' => $stream
                ]
            );
    }

    public function flush()
    {
        $this->entityManager->flush();
    }
}
