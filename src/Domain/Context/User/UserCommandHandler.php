<?php

declare(strict_types=1);

namespace App\Domain\Context\User;

use App\Domain\Context\User\Command\CreateUser;
use App\Domain\Context\User\Command\UpdateUser;
use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Infrastructure\EventSourcing\AppEventStore;
use App\Infrastructure\EventSourcing\CommandResult;
use App\Infrastructure\EventSourcing\Events;
use Neos\EventStore\Model\EventStream\EventStreamInterface;
use Ramsey\Uuid\Uuid;

class UserCommandHandler
{
    public function __construct(
        private readonly AppEventStore $eventStore
    ) {
    }

    public function handleCreateUser(CreateUser $command): CommandResult
    {
        $this->requireUserNotToExist($command->getName(), $command->getMail());

        $userIdentifier = UserIdentifier::fromString(Uuid::uuid4()->toString());
        $event = new UserWasCreated($userIdentifier, $command->getName(), $command->getMail());

        $commandResult = $this->eventStore->commit($userIdentifier, Events::with($event));
        $commandResult->block();

        return $commandResult;
    }

    public function handleUpdateUser(UpdateUser $command): CommandResult
    {
        $event = new UserWasUpdated($command->getId(), $command->getName(), $command->getMail());
        $commandResult = $this->eventStore->commit($command->getId(), Events::with($event));
        $commandResult->block();

        return $commandResult;
    }

    public function handleStream(UserIdentifier $userIdentifier): EventStreamInterface
    {
        return $this->eventStore->load($userIdentifier);
    }

    private function requireUserNotToExist(string $name, string $mail)
    {
        // read a model, throw exception if it does not exist.
        // This would be a soft constraint check.

        // or, build up an Aggregate and ask it; this would be a hard constraint check.
        // If in doubt, use soft constraints :)
    }
}
