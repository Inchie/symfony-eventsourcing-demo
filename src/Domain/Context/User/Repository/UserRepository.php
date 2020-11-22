<?php

declare(strict_types=1);

namespace App\Domain\Context\User\Repository;

use App\Domain\Context\User\Model\User;

interface UserRepository
{
    public function add(User $user);

    public function findAll();

    public function findByStream($stream);

    public function flush();
}
