<?php

namespace App\Domain\Context\User;

use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Context\User\Store\UserEventStore;
use Neos\EventSourcing\Event\DomainEvents;
use Neos\EventSourcing\EventStore\EventStore;
use Neos\EventSourcing\EventStore\StreamName;

class UserCommandHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(UserEventStore $eventStore)
    {
        $this->eventStore = $eventStore->create();

        // TODO: also inject finders from projections which you want :)
    }

    /**
     * @param CreateUser $command
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function handleCreateUser(CreateUser $command)
    {
        $this->requireUserToExist(
            $command->getName(),
            $command->getMail()
        );

        $streamName = sprintf(
            'User:%s',
            uniqid()
        );

        $event = new UserWasCreated(
            $command->getName(),
            $command->getMail(),
            $streamName
        );

        $stream = StreamName::fromString($streamName);

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleUpdateUser(UpdateUser $command)
    {
        $streamName = $command->getStream();

        $event = new UserWasUpdated(
            $command->getName(),
            $command->getMail(),
            $command->getStream()
        );

        $stream = StreamName::fromString($streamName);

        $this->eventStore->commit($stream, DomainEvents::withSingleEvent(
            $event
        ));
    }

    public function handleStream($streamName)
    {
        return $this->eventStore->load(StreamName::fromString($streamName));
    }

    private function requireUserToExist(string $name, string $mail)
    {
        // read a model, throw exception if it does not exist.
        // This would be a soft constraint check.

        // or, build up an Aggregate and ask it; this would be a hard constraint check.
        // If in doubt, use soft constraints :)
    }
}
