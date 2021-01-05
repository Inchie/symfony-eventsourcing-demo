<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Event;

use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\User\UserIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class BlogWasCreated implements DomainEventInterface
{
    /**
     * @var BlogIdentifier
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var UserIdentifier
     */
    private $author;

    public function __construct(
        BlogIdentifier $id,
        string $name,
        UserIdentifier $author
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
    }

    public function getId(): BlogIdentifier
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): UserIdentifier
    {
        return $this->author;
    }
}
