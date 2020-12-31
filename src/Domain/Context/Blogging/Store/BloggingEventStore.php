<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Store;

use Symfony\Component\HttpKernel\KernelInterface;

class BloggingEventStore
{
    private $eventStore;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->eventStore = $kernelInterface
            ->getContainer()
            ->get('neos_eventsourcing.eventstore.blog');
    }

    public function create()
    {
        return $this->eventStore;
    }
}
