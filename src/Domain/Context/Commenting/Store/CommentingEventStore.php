<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting\Store;

use Symfony\Component\HttpKernel\KernelInterface;

class CommentingEventStore
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
