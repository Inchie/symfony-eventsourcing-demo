<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing;

use App\Domain\Context\Blogging\Event\BlogWasCreated;
use App\Domain\Context\Blogging\Event\BlogWasUpdated;
use App\Domain\Context\Blogging\Event\CommentWasCreated;
use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use JsonException;
use Neos\EventStore\Model\Event;

final class EventNormalizer
{
    private array $knownEvents = [
        'UserWasCreated' => UserWasCreated::class,
        'UserWasUpdated' => UserWasUpdated::class,
        'BlogWasCreated' => BlogWasCreated::class,
        'BlogWasUpdated' => BlogWasUpdated::class,
        'CommentWasCreated' => CommentWasCreated::class,
    ];

    public function getEventData(EventInterface $event): Event\EventData
    {
        try {
            $eventDataAsJson = json_encode($event, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Failed to normalize event of type "%s": %s',
                    get_debug_type($event),
                    $exception->getMessage()
                ),
                1_651_838_981
            );
        }
        return Event\EventData::fromString($eventDataAsJson);
    }

    public function getEventType(EventInterface $event): Event\EventType
    {
        return Event\EventType::fromString((new \ReflectionClass($event))->getShortName());
    }

    public function denormalize(Event $event): EventInterface
    {
        /** @var class-string<EventInterface> $eventClassName */
        $eventClassName = $this->resolveFullyQualifiedClassname($event);

        try {
            $eventDataAsArray = json_decode($event->data->value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new \InvalidArgumentException(
                sprintf('Failed to decode data of event "%s": %s', $event->id->value, $exception->getMessage()),
                1_651_839_461
            );
        }

        assert(is_array($eventDataAsArray));
        return $eventClassName::fromArray($eventDataAsArray);
    }

    private function resolveFullyQualifiedClassname(Event $event): string
    {
        if (array_key_exists($event->type->value, $this->knownEvents)) {
            return $this->knownEvents[$event->type->value];
        }

        throw new \InvalidArgumentException(
            sprintf('Failed to denormalize event "%s" of type "%s"', $event->id->value, $event->type->value),
            1_651_839_705
        );
    }
}
