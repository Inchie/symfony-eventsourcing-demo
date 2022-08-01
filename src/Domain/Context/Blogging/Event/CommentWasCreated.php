<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Event;

use App\Domain\Context\Blogging\ValueObject\BlogIdentifier;
use App\Domain\Context\User\ValueObject\UserIdentifier;
use App\Infrastructure\EventSourcing\EventInterface;

class CommentWasCreated implements EventInterface
{
    public function __construct(
        private readonly BlogIdentifier $blogIdentifier,
        private readonly UserIdentifier $userIdentifier,
        private readonly string $comment
    ) {
    }

    public function getBlogIdentifier(): BlogIdentifier
    {
        return $this->blogIdentifier;
    }

    public function getUserIdentifier(): UserIdentifier
    {
        return $this->userIdentifier;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public static function fromArray(array $values): EventInterface
    {
        return new self(
            BlogIdentifier::fromString($values['blogIdentifier']),
            UserIdentifier::fromString($values['userIdentifier']),
            $values['comment']
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'blogIdentifier' => $this->blogIdentifier,
            'userIdentifier' => $this->userIdentifier,
            'comment' => $this->comment,
        ];
    }
}
