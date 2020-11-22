<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Infrastructure;

use App\Domain\Context\User\Model\User;
use App\Domain\Context\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(User $user)
    {
        $this->entityManager->persist($user);
    }

    public function findAll()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findAll();
    }

    public function findByStream($stream)
    {
        return $this->entityManager
            ->getRepository(User::class)
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
