<?php

declare(strict_types=1);

namespace App\Domain\Projection\User\Infrastructure;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\User;
use App\Domain\Projection\User\UserIdentifier;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class DoctrineUserRepository implements UserRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function nextIdentity(): UserIdentifier
    {
        return UserIdentifier::fromUuid(
            Uuid::uuid4()
        );
    }

    public function findAll()
    {
        $query = "
            SELECT 
                id,
                name,
                mail   
            FROM `user`   
        ";

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                []
            )
            ->fetchAll();

        $users = new \ArrayIterator();
        foreach ($records as $row) {
            $users->append(
                User::create(
                    $row['id'],
                    $row['name'],
                    $row['mail']
                )
            );
        }
        return $users;
    }

    public function findById(UserIdentifier $userIdentifier): User
    {
        $query = "
            SELECT 
                id,
                name,
                mail   
            FROM `user`  
            WHERE id = :id
        ";

        $queryParams = [
            'id' => $userIdentifier->jsonSerialize()
        ];

        $record = $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            )
            ->fetch();

        if ($record === false) {
            throw new \Exception('Could not find the user with the given id');
        }

        return User::create(
            $record['id'],
            $record['name'],
            $record['mail']
        );
    }

    public function addByEvent(UserWasCreated $event)
    {
        $query = "
            INSERT INTO `user`
            SET
                `id` = :id,
                `name` = :name,
                `mail` = :mail
            ";

        // set query params
        $queryParams = [
            'id' => $event->getId()->jsonSerialize(),
            'name' => $event->getName(),
            'mail' => $event->getMail()
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery(
                $query,
                $queryParams
            );
    }

    public function updateByEvent(UserWasUpdated $event)
    {
        $query = "
            UPDATE `user`
            SET
                `name` = :name,
                `mail` = :mail
            where id = :id
        ";

        // set query params
        $queryParams = [
            'id' => $event->getId()->jsonSerialize(),
            'name' => $event->getName(),
            'mail' => $event->getMail()
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
            TRUNCATE TABLE user;
        ";

        $this->entityManager
            ->getConnection()
            ->exec($query);
    }
}
