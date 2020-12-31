<?php

declare(strict_types=1);

namespace App\Domain\Context\Blog\Event;

use Neos\EventSourcing\Event\DomainEventInterface;

class BlogWasUpdated implements DomainEventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $streamName;

    /**
     * BlogWasUpdated constructor.
     * @param string $name
     * @param string $streamName
     */
    public function __construct(
        string $name,
        string $streamName
    ) {
        $this->name = $name;
        $this->streamName = $streamName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }
}
