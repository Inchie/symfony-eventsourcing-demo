<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

use App\Domain\Projection\User\Repository\UserRepository;

class UserFinder
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function execute()
    {
        return $this->userRepository->findAll();
    }
}
