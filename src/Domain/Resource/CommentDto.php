<?php

declare(strict_types=1);

namespace App\Domain\Resource;

class CommentDto
{
    public $author;

    public $comment;

    public function fromPayload($payload)
    {
        $properties = json_decode($payload);

        $comment = new self();
        $comment->author = $properties->authorIdentifier;
        $comment->comment = $properties->comment;

        return $comment;
    }
}
