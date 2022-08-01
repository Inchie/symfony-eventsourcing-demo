<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Projection\ProjectionTrait;
use App\Domain\Projection\User\Repository\UserRepository;
use App\Infrastructure\EventSourcing\EventNormalizer;
use App\Infrastructure\EventSourcing\Projection\ProjectionInterface;
use Doctrine\DBAL\Connection;
use Neos\EventStore\Model\Event\SequenceNumber;

class UserProjection implements ProjectionInterface
{
    use ProjectionTrait;

    public function __construct(
        private readonly Connection $connection,
        private readonly UserRepository $userRepository,
        private readonly EventNormalizer $eventNormalizer
    ) {
    }

    public function whenUserWasCreated(UserWasCreated $event)
    {
        $this->userRepository->addByEvent($event);
    }

    public function whenUserWasUpdated(UserWasUpdated $event)
    {
        $this->userRepository->updateByEvent($event);
    }

    public function reset(): void
    {
        $this->userRepository->truncate();
        $this->getCheckpointStorage()
            ->updateAndReleaseLock(SequenceNumber::fromInteger(0));
    }

    public static function getCheckpointSubscriberId(): string
    {
        return 'User';
    }
}
