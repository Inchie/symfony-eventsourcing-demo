<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

use Doctrine\ORM\Mapping as ORM;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mail;

    public static function create(
        string $id,
        string $name,
        string $mail
    ): self
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
