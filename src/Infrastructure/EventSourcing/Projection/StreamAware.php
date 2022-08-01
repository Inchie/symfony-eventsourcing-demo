<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing\Projection;

use Neos\EventStore\Model\Event\StreamName;

interface StreamAware
{
    public function streamName(): StreamName;
}
