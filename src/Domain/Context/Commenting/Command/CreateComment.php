<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting\Command;

use App\Domain\ValueObject\UserIdentifier;

class CreateComment
{
    /**
     * @var UserIdentifier
     */
    private $authorIdentifier;

    /**
     * @var string
     */
    private $comment;

    /**
     * @param UserIdentifier $authorIdentifier
     * @param string $comment
     */
    public function __construct(UserIdentifier $authorIdentifier, string $comment)
    {
        $this->authorIdentifier = $authorIdentifier;
        $this->comment = $comment;
    }

    /**
     * @return UserIdentifier
     */
    public function getAuthorIdentifier(): UserIdentifier
    {
        return $this->authorIdentifier;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
