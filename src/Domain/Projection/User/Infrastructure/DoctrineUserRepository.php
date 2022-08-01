<?php

declare(strict_types=1);

namespace App\Domain\Projection\User\Infrastructure;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class DoctrineUserRepository implements UserRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function nextIdentity(): UserIdentifier
    {
        #return UserIdentifier::fromUuid(Uuid::uuid4());
    }

    public function findAll()
    {
        $query = '
            SELECT 
                id,
                name,
                mail   
            FROM `user`   
        ';

        $records = $this->entityManager
            ->getConnection()
            ->executeQuery($query, [])
            ->fetchAllAssociative();

        $users = new \ArrayIterator();
        foreach ($records as $row) {
            $users->append(User::create($row['id'], $row['name'], $row['mail']));
        }
        return $users;
    }

    public function findById(UserIdentifier $userIdentifier): User
    {
        $query = '
            SELECT 
                id,
                name,
                mail   
            FROM `user`  
            WHERE id = :id
        ';

        $queryParams = [
            'id' => $userIdentifier->getValue(),
        ];

        $record = $this->entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams)
            ->fetchAssociative();

        if ($record === false) {
            throw new \Exception('Could not find the user with the given id');
        }

        return User::create($record['id'], $record['name'], $record['mail']);
    }

    public function addByEvent(UserWasCreated $event)
    {
        $query = '
            INSERT INTO `user`
            SET
                `id` = :id,
                `name` = :name,
                `mail` = :mail
            ';

        // set query params
        $queryParams = [
            'id' => $event->getId()
                ->getValue(),
            'name' => $event->getName(),
            'mail' => $event->getMail(),
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams);
    }

    public function updateByEvent(UserWasUpdated $event)
    {
        $query = '
            UPDATE `user`
            SET
                `name` = :name,
                `mail` = :mail
            where id = :id
        ';

        // set query params
        $queryParams = [
            'id' => $event->getId()
                ->jsonSerialize(),
            'name' => $event->getName(),
            'mail' => $event->getMail(),
        ];

        $this->entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams);
    }

    public function truncate()
    {
        $query = '
            TRUNCATE TABLE user;
        ';

        $this->entityManager
            ->getConnection()
            ->exec($query);
    }
}
