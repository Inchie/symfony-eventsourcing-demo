<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Ramsey\Uuid\Uuid;

/**
 * User Identifier
 */
final class BlogIdentifier implements \JsonSerializable
{
    /**
     * @var string
     */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function create(): self
    {
        return new static(Uuid::uuid4()->toString());
    }

    // MAGICALLY CALLED during deserialization.
    public static function fromString(string $value): self
    {
        return new static($value);
    }

    // MAGICALLY CALLED during serialization
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    // only for debugging, can have any output
    public function __toString(): string
    {
        return $this->value;
    }

    // sometimes, also an equals method is helpful.
}
