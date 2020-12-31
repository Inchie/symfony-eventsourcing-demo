<?php

declare(strict_types=1);

namespace App\Domain\Context\Blog\Command;

class UpdateBlog
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $stream;

    public function __construct(
        string $name,
        string $stream
    )
    {
        $this->name = $name;
        $this->stream = $stream;
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
    public function getStream(): string
    {
        return $this->stream;
    }
}
