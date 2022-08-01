<?php

declare(strict_types=1);

namespace App\Domain\Context\User\ValueObject;

use App\Infrastructure\EventSourcing\Projection\StreamAware;
use Neos\EventStore\Model\Event\StreamName;

final class UserIdentifier implements \JsonSerializable, StreamAware, \Stringable
{
    private function __construct(private readonly string $value)
    {
    }

    public function __toString(): string
    {
        return sprintf('User(%d)', $this->value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function streamName(): StreamName
    {
        return StreamName::fromString(sprintf('User:%s', $this->value));
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
