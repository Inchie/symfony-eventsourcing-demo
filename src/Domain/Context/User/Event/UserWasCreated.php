<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Event;

use App\Domain\Projection\User\UserIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class UserWasCreated implements DomainEventInterface
{
    /**
     * @var UserIdentifier
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    public function __construct(
        UserIdentifier $id,
        string $name,
        string $mail
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->mail = $mail;
    }

    public function getId(): UserIdentifier
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMail(): string
    {
        return $this->mail;
    }
}
