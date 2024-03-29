<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Store;

use Symfony\Component\HttpKernel\KernelInterface;

class UserEventStore
{
    public const USER_STREAM_NAME = 'user';

    private $kernelInterface;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->kernelInterface = $kernelInterface;
    }

    public function create()
    {
        return $this->kernelInterface
            ->getContainer()
            ->get('neos_eventsourcing.eventstore.user');
    }
}
