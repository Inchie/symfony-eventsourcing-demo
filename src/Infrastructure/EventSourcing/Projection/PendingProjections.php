<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Projection;

use Neos\EventStore\Model\Event\SequenceNumber;
use Neos\EventStore\Model\Events;

/**
 * @copyright escr-test-distribution
 * This object is built in and contains all projections that were affected by the handled
 * command and their respective target sequence number. This can be used to block until the affected projections
 * are "up to date"
 *
 * @implements \IteratorAggregate<ProjectionInterface>
 */
final class PendingProjections implements \IteratorAggregate
{
    /**
     * @param Projections<ProjectionInterface> $projections
     * @param array<string, int> $sequenceNumberPerProjection
     */
    private function __construct(
        public Projections $projections,
        public array $sequenceNumberPerProjection
    ) {
    }

    public static function fromProjectionsAndEventsAndSequenceNumber(
        Projections $allProjections,
        Events $events,
        SequenceNumber $sequenceNumber
    ): self {
        $sequenceNumberInteger = $sequenceNumber->value - $events->count() + 1;
        $pendingProjections = Projections::create();
        $sequenceNumberPerProjection = [];
        foreach ($events as $event) {
            foreach ($allProjections as $projection) {
                if ($projection->canHandle($event)) {
                    $sequenceNumberPerProjection[$projection::class] = $sequenceNumberInteger;
                    if (! $pendingProjections->has($projection::class)) {
                        $pendingProjections = $pendingProjections->with($projection);
                    }
                }
            }
            $sequenceNumberInteger++;
        }
        return new self($pendingProjections, $sequenceNumberPerProjection);
    }

    public static function empty(): self
    {
        return new self(Projections::create(), []);
    }

    public function getExpectedSequenceNumber(ProjectionInterface $projection): SequenceNumber
    {
        if (! array_key_exists($projection::class, $this->sequenceNumberPerProjection)) {
            throw new \InvalidArgumentException(sprintf(
                'Projection of class "%s" is not pending',
                $projection::class
            ), 1_652_252_976);
        }
        return SequenceNumber::fromInteger($this->sequenceNumberPerProjection[$projection::class]);
    }

    public function getIterator(): \Traversable
    {
        return $this->projections->getIterator();
    }
}
