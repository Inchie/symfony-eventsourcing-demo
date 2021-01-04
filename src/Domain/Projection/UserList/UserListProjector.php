<?php

declare(strict_types=1);

namespace App\Domain\Projection\UserList;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Context\User\Model\User;
use App\Domain\Context\User\Repository\UserRepository;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\Projection\ProjectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserListProjector implements ProjectorInterface, EventSubscriberInterface
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
        $newUser = User::create(
            $event->getName(),
            $event->getMail(),
            $event->getStreamName()
        );
        $this->userRepository->add($newUser);
        $this->userRepository->flush();
    }

    public function whenUserWasUpdated(UserWasUpdated $event, RawEvent $rawEvent)
    {
        $user = $this->userRepository->findByStream($event->getStreamName());
        $user->update(
            $event->getName(),
            $event->getMail()
        );

        $this->userRepository->add($user);
        $this->userRepository->flush();
    }

    public function reset(): void
    {
        throw new \Exception('Reset is not supported at the moment');
    }
}
