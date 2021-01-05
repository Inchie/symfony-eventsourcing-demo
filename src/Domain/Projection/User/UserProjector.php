<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Projection\User\Repository\UserRepository;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserProjector implements ProjectorInterface, EventSubscriberInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            // NOTE!!! You always have to use "when*" namings, as otherwise, the EventListenerInvoker
            // will not properly call the right methods here.

            // We only use the EventSubscriber from symfony to figure out which listeners should be called.
            UserWasCreated::class => ['whenUserWasCreated'],
            UserWasUpdated::class => ['whenUserWasUpdated']
        ];
    }

    public function whenUserWasCreated(UserWasCreated $event, RawEvent $rawEvent)
    {
        $this->userRepository->addByEvent($event);
    }

    public function whenUserWasUpdated(UserWasUpdated $event, RawEvent $rawEvent)
    {
        $this->userRepository->updateByEvent($event);
    }

    public function reset(): void
    {
        $this->userRepository->truncate();
    }
}
