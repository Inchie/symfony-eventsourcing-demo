<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Symfony\Resource;

class CatchUpMessage
{
    public array $projectionClassNames;
}
