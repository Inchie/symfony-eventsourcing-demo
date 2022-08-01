<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSourcing;

interface EventInterface extends \JsonSerializable
{
    public static function fromArray(array $values): self;
}
