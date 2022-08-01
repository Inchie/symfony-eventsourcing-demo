<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Symfony\Handler;

use App\Infrastructure\EventSourcing\AppEventStore;
use App\Infrastructure\EventSourcing\Symfony\Resource\CatchUpMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CatchUpMessageHandler implements MessageHandlerInterface
{
    public function __construct(private readonly AppEventStore $appEventStore)
    {
    }

    public function __invoke(CatchUpMessage $message)
    {
        foreach ($message->projectionClassNames as $projectionClassName) {
            $this->appEventStore->catchUpProjection($projectionClassName);
        }
    }
}
