<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Event;

use Neos\EventSourcing\Event\DomainEventInterface;

class UserWasCreated implements DomainEventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $streamName;

    /**
     * UserWasCreated constructor.
     * @param string $name
     * @param string $mail
     * @param string $streamName
     */
    public function __construct(
        string $name,
        string $mail,
        string $streamName
    )
    {
        $this->name = $name;
        $this->mail = $mail;
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
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getStreamName(): string
    {
        return $this->streamName;
    }
}
