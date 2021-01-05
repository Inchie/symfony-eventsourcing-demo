<?php

declare(strict_types=1);

namespace App\Domain\Context\Blog\Event;

use App\Domain\Projection\Blog\BlogIdentifier;
use Neos\EventSourcing\Event\DomainEventInterface;

class BlogWasUpdated implements DomainEventInterface
{
    /**
     * @var BlogIdentifier
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        BlogIdentifier $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): BlogIdentifier
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
