<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Projection;

/**
 * @copyright escr-test-distribution
 *
 * An immutable set of Content Repository projections ({@see ProjectionInterface}
 *
 * @implements \IteratorAggregate<ProjectionInterface>
 */
final class Projections implements \IteratorAggregate
{
    /**
     * @var array<class-string<ProjectionInterface>, ProjectionInterface>
     */
    private array $projections;

    /**
     * @param array<class-string<ProjectionInterface> $projections
     */
    private function __construct(ProjectionInterface ...$projections)
    {
        $this->projections = [];
        foreach ($projections as $projection) {
            $this->projections[$projection::class] = $projection;
        }
    }

    public static function create(ProjectionInterface ...$projections): self
    {
        return new self(...$projections);
    }

    /**
     * Find all projections which need to be updated because of the passed-in $events.
     *
     * Calculated by calling {@see ProjectionInterface::canHandle()}.
     *
     * @return $this
     */
    // Later: Events $events
    public function retrievePendingProjectionsForEvents(iterable $events): self
    {
        $pendingProjections = self::create();
        foreach ($events as $event) {
            foreach ($this as $projection) {
                if ($projection->canHandle($event)) {
                    if (! $pendingProjections->has($projection::class)) {
                        $pendingProjections = $pendingProjections->with($projection);
                    }
                }
            }
        }
        return $pendingProjections;
    }

    public function with(ProjectionInterface $projection): self
    {
        if ($this->has($projection::class)) {
            throw new \InvalidArgumentException(
                sprintf('a projection of type "%s" already exists in this set', $projection::class),
                1_650_121_280
            );
        }
        $projections = $this->projections;
        $projections[] = $projection;
        return new self(...array_values($projections));
    }

    /**
     * @template T of Projection
     * @param class-string<T> $projectionClassName
     * @return T
     */
    public function get(string $projectionClassName): ProjectionInterface
    {
        if (! $this->has($projectionClassName)) {
            throw new \InvalidArgumentException(
                sprintf('a projection of type "%s" is not part of this set', $projectionClassName),
                1_650_120_813
            );
        }
        return $this->projections[$projectionClassName];
    }

    public function has(string $projectionClassName): bool
    {
        return array_key_exists($projectionClassName, $this->projections);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->projections);
    }

    public function getClassNames(): array
    {
        return array_keys($this->projections);
    }
}
