<?php

declare(strict_types=1);

namespace App\Domain\Projection\User;

use App\Domain\Projection\User\Repository\UserRepository;

class UserFinder
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function execute()
    {
        return $this->userRepository->findAll();
    }
}
