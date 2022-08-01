<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing;

use App\Domain\Projection\Blog\BlogProjection;
use App\Domain\Projection\User\UserProjection;
use App\Infrastructure\EventSourcing\Projection\PendingProjections;
use App\Infrastructure\EventSourcing\Projection\ProjectionCatchUpTriggerInterface;
use App\Infrastructure\EventSourcing\Projection\Projections;
use App\Infrastructure\EventSourcing\Projection\StreamAware;
use Doctrine\DBAL\Connection;
use Neos\EventStore\DoctrineAdapter\DoctrineEventStore;
use Neos\EventStore\EventStoreInterface;
use Neos\EventStore\Model\Event;
use Neos\EventStore\Model\Events;
use Neos\EventStore\Model\EventStream\EventStreamInterface;
use Neos\EventStore\Model\EventStream\ExpectedVersion;

final class AppEventStore
{
    private readonly EventStoreInterface $eventStore;

    private readonly Projections $projections;

    public function __construct(
        Connection $connection,
        private readonly ProjectionCatchUpTriggerInterface $projectionCatchUpTrigger,
        private readonly EventNormalizer $eventNormalizer,
        BlogProjection $blogProjection,
        UserProjection $userProjection
    ) {
        $eventStore = new DoctrineEventStore($connection, 'symfony_demo_events');

        $this->eventStore = $eventStore;
        $this->projections = Projections::create($blogProjection, $userProjection);
    }

    public function commit(
        StreamAware $streamName,
        \App\Infrastructure\EventSourcing\Events $events,
        ExpectedVersion $expectedVersion = null
    ): CommandResult {
        if ($expectedVersion === null) {
            $expectedVersion = ExpectedVersion::ANY();
        }

        $writableEventsList = $events->map(function ($event) {
            assert($event instanceof EventInterface);

            $eventMetadata = Event\EventMetadata::none();
            $eventId = Event\EventId::create();

            if ($event instanceof EventWithMetadata) {
                $eventId = $event->eventId;
                $eventMetadata = $event->toMetadata();

                // now continue processing the inner event
                $event = $event->innerEvent;
            }
            assert($event instanceof \JsonSerializable);

            return new Event(
                $eventId,
                $this->eventNormalizer->getEventType($event),
                $this->eventNormalizer->getEventData($event),
                $eventMetadata
            );
        });
        if (count($writableEventsList) === 0) {
            return CommandResult::empty();
        }
        $writableEvents = Events::fromArray($writableEventsList);

        $commitResult = $this->eventStore->commit($streamName->streamName(), $writableEvents, $expectedVersion);
        $pendingProjections = PendingProjections::fromProjectionsAndEventsAndSequenceNumber(
            $this->projections,
            $writableEvents,
            $commitResult->highestCommittedSequenceNumber
        );

        $this->projectionCatchUpTrigger->triggerCatchUp($pendingProjections->projections);

        return new CommandResult($pendingProjections);
    }

    public function load(StreamAware $streamName): EventStreamInterface
    {
        return $this->eventStore->load($streamName->streamName());
    }

    public function catchUpProjection(string $projectionClassName): void
    {
        $projection = $this->projections->get($projectionClassName);

        $projection->catchUp($this->eventStore);
    }

    public function catchUpAll(): void
    {
        foreach ($this->projections as $projection) {
            $projection->catchUp($this->eventStore);
        }
    }

    public function projections(): Projection\Projections
    {
        return $this->projections;
    }
}
