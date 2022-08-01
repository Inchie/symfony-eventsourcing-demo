<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Projection;

/**
 * Interface for a class that (asynchronously) triggers a catchup of affected projections
 * after a {@see AppEventStore::commit()} call
 */
interface ProjectionCatchUpTriggerInterface
{
    public function triggerCatchUp(Projections $projections): void;
}
