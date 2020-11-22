<?php

declare(strict_types=1);

namespace App\Domain\Projection\UserList;

use App\Domain\Context\User\Repository\UserRepository;

class UserListFinder
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
