<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing;

use Neos\EventStore\Model\Event\EventId;
use Neos\EventStore\Model\Event\EventMetadata;

final class EventWithMetadata implements EventInterface
{
    private function __construct(
        /**
         * @readonly
         */
        public EventInterface $innerEvent,
        /**
         * @readonly
         */
        public EventId $eventId,
        /**
         * The PREVIOUS event which triggered this event
         *
         * @readonly
         */
        public ?\Neos\EventStore\Model\Event\EventId $causation,
        /**
         * The ROOT CAUSE which triggered this event
         *
         * @readonly
         */
        public ?\Neos\EventStore\Model\Event\EventId $correlation
    ) {
    }

    public static function forEvent(EventInterface $innerEvent): self
    {
        return new self($innerEvent, EventId::create(), null, null);
    }

    public function withParentEvent(?self $parentEvent): self
    {
        if ($parentEvent === null) {
            return $this;
        }
        return new self(
            $this->innerEvent,
            $this->eventId,
            $parentEvent->eventId,
            $parentEvent->correlation ?? $parentEvent->eventId
        );
    }

    public function toMetadata(): EventMetadata
    {
        $metadata = [];

        if ($this->causation !== null) {
            $metadata['causationIdentifier'] = $this->causation->value;
        }

        if ($this->correlation !== null) {
            $metadata['correlationIdentifier'] = $this->correlation->value;
        }

        return EventMetadata::fromArray($metadata);
    }

    public static function fromArray(array $values): EventInterface
    {
        throw new \RuntimeException('Does not make sense');
    }

    public function jsonSerialize()
    {
        throw new \RuntimeException('Does not make sense');
    }
}
