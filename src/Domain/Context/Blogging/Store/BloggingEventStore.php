<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Store;

use Symfony\Component\HttpKernel\KernelInterface;

class BloggingEventStore
{
    private $kernelInterface;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->kernelInterface = $kernelInterface;
    }

    public function create()
    {
        return $this->kernelInterface
            ->getContainer()
            ->get('neos_eventsourcing.eventstore.blog');
    }
}
