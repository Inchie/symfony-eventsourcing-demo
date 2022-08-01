<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Symfony;

use App\Infrastructure\EventSourcing\Projection\ProjectionCatchUpTriggerInterface;
use App\Infrastructure\EventSourcing\Projection\Projections;
use App\Infrastructure\EventSourcing\Symfony\Resource\CatchUpMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ProjectionCatchUpTrigger implements ProjectionCatchUpTriggerInterface
{
    private const NAMESPACE_TO_RESOURCE = 'src\Infrastructure\EventSourcing\Symfony\Resource';

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function triggerCatchUp(Projections $projections): void
    {
        $message = new CatchUpMessage();
        $message->projectionClassNames = $projections->getClassNames();
        $this->messageBus->dispatch($message);
    }
}
