<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $mail;

    /**
     * @ORM\Column(type="text")
     */
    private $stream;

    private function __construct()
    {
    }

    public static function create(
        string $name,
        string $mail,
        string $stream
    )
    {
        $newUser = new self();
        $newUser->name = $name;
        $newUser->mail = $mail;
        $newUser->stream = $stream;

        return $newUser;
    }

    public function update(
        string $name,
        string $mail
    )
    {
        $this->name = $name;
        $this->mail = $mail;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getStream()
    {
        return $this->stream;
    }
}
