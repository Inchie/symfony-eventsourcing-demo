<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Command;

use App\Domain\Projection\Blog\BlogIdentifier;

class UpdateBlog
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
    )
    {
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
