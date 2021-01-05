<?php

declare(strict_types=1);

namespace App\Domain\Context\Blogging\Command;

use App\Domain\Projection\User\UserIdentifier;

class CreateBlog
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var UserIdentifier
     */
    private $authorIdentifier;

    public function __construct(string $name, UserIdentifier $authorIdentifier)
    {
        $this->name = $name;
        $this->authorIdentifier = $authorIdentifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return UserIdentifier
     */
    public function getAuthorIdentifier(): UserIdentifier
    {
        return $this->authorIdentifier;
    }
}
