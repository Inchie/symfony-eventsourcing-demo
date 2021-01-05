<?php

declare(strict_types=1);

namespace App\Domain\Context\User;

use App\Domain\Context\Blogging\Store\BloggingEventStore;
use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Context\User\Store\UserEventStore;
use App\Domain\Helper\StreamNameHelper;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Domain\Projection\User\UserIdentifier;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\EventStore;
use Neos\EventSourcing\EventStore\StreamName;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserCommandHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserEventStore $eventStore,
        UserRepository $userRepository
    )
    {
        $this->eventStore = $eventStore->create();
        $this->userRepository = $userRepository;
    }

    /**
     * @param CreateUser $command
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function handleCreateUser(
        CreateUser $command
    )
    {
        $this->requireUserNotToExist(
            $command->getName(),
            $command->getMail()
        );

        $uuid = $this->userRepository->nextIdentity();
        $event = new UserWasCreated(
            $uuid,
            $command->getName(),
            $command->getMail()
        );

        $stream = StreamNameHelper::fromString(
            UserEventStore::USER_STREAM_NAME,
            $uuid
        );

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleUpdateUser(UpdateUser $command)
    {
        $event = new UserWasUpdated(
            $command->getId(),
            $command->getName(),
            $command->getMail()
        );

        $stream = StreamNameHelper::fromString(
            UserEventStore::USER_STREAM_NAME,
            $command->getId()
        );

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleStream(UserIdentifier $userIdentifier)
    {
        $stream = StreamNameHelper::fromString(
            UserEventStore::USER_STREAM_NAME,
            $userIdentifier
        );

        return $this->eventStore->load($stream);
    }

    private function requireUserNotToExist(string $name, string $mail)
    {
        // read a model, throw exception if it does not exist.
        // This would be a soft constraint check.

        // or, build up an Aggregate and ask it; this would be a hard constraint check.
        // If in doubt, use soft constraints :)
    }
}
