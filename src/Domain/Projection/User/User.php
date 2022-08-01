<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

class User
{
    private ?string $id = null;

    private ?string $name = null;

    private ?string $mail = null;

    public static function create(string $id, string $name, string $mail): self
    {
        $newUser = new self();
        $newUser->id = $id;
        $newUser->name = $name;
        $newUser->mail = $mail;
        return $newUser;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMail(): string
    {
        return $this->mail;
    }
}
