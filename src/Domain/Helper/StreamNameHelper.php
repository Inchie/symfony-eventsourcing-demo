<?php

declare(strict_types=1);

namespace App\Domain\Helper;

use Neos\EventSourcing\EventStore\StreamName;

final class StreamNameHelper
{
    public static function fromString(
        string $context,
        \JsonSerializable $uuid
    ): StreamName
    {
        $value = sprintf(
            '%s-%s',
            $context,
            $uuid->jsonSerialize()
        );

        return StreamName::fromString(
            $value
        );
    }
}
