<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Projection;

use Neos\EventStore\EventStoreInterface;
use Neos\EventStore\Model\Event;
use Neos\EventStore\Model\Event\SequenceNumber;

/**
 * Common interface for a projection
 */
interface ProjectionInterface
{
    public function canHandle(Event $event): bool;

    public function catchUp(EventStoreInterface $eventStore): void;

    public function getSequenceNumber(): SequenceNumber;

    public function reset(): void;
}
