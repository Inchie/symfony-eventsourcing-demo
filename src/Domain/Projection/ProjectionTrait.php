<?php

declare(strict_types=1);

namespace App\Domain\Projection;

use Neos\EventStore\CatchUp\CatchUp;
use Neos\EventStore\DoctrineAdapter\DoctrineCheckpointStorage;
use Neos\EventStore\EventStoreInterface;
use Neos\EventStore\Model\Event;
use Neos\EventStore\Model\Event\SequenceNumber;
use Neos\EventStore\Model\EventEnvelope;
use Neos\EventStore\Model\EventStream\VirtualStreamName;

trait ProjectionTrait
{
    abstract public static function getCheckpointSubscriberId(): string;

    public function canHandle(Event $event): bool
    {
        return method_exists($this, 'when' . $event->type->value);
    }

    public function catchUp(EventStoreInterface $eventStore): void
    {
        $eventStream = $eventStore->load(VirtualStreamName::forCategory($this->getProjectionName()));

        $checkpointStorage = $this->getCheckpointStorage();

        $catchUp = CatchUp::create(function (EventEnvelope $eventEnvelope) {
            $this->apply($eventEnvelope);
        }, $checkpointStorage);

        $catchUp->run($eventStream);
    }

    public function getSequenceNumber(): SequenceNumber
    {
        return $this->getCheckpointStorage()
            ->getHighestAppliedSequenceNumber();
    }

    private function apply(EventEnvelope $eventEnvelope)
    {
        if (! $this->canHandle($eventEnvelope->event)) {
            return;
        }
        $eventInstance = $this->eventNormalizer->denormalize($eventEnvelope->event);
        $this->{'when' . $eventEnvelope->event->type->value}($eventInstance, $eventEnvelope);
    }

    private function getCheckpointStorage(): DoctrineCheckpointStorage
    {
        return new DoctrineCheckpointStorage(
            $this->connection,
            'neos_eventsourcing_eventlistener_appliedeventslog',
            self::getCheckpointSubscriberId()
        );
    }

    private function getProjectionName(): string
    {
        return sprintf('%s:', self::getCheckpointSubscriberId());
    }
}
