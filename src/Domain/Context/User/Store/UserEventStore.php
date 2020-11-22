<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Store;

use Symfony\Component\HttpKernel\KernelInterface;

class UserEventStore
{
    private $eventStore;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->eventStore = $kernelInterface
            ->getContainer()
            ->get('neos_eventsourcing.eventstore.user');
    }

    public function create()
    {
        return $this->eventStore;
    }
}
