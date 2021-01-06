<?php

declare(strict_types=1);

namespace App\Domain\Context\Commenting\Command;

use App\Domain\Projection\Blog\BlogIdentifier;
use App\Domain\Projection\User\UserIdentifier;

class CreateComment
{
    /**
     * @var BlogIdentifier
     */
    private $blogIdentifier;

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
    public function __construct(
        BlogIdentifier $blogIdentifier,
        UserIdentifier $authorIdentifier,
        string $comment
    ) {
        $this->blogIdentifier = $blogIdentifier;
        $this->authorIdentifier = $authorIdentifier;
        $this->comment = $comment;
    }

    public function getBlogIdentifier(): BlogIdentifier
    {
        return $this->blogIdentifier;
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
