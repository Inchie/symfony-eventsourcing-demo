<?php

declare(strict_types=1);

namespace App\Domain\Projection\User\Repository;

use App\Domain\Context\User\Event\UserWasCreated;
use App\Domain\Context\User\Event\UserWasUpdated;
use App\Domain\Projection\User\User;
use App\Domain\Projection\User\UserIdentifier;

interface UserRepository
{
    public function nextIdentity(): UserIdentifier;

    public function findById(UserIdentifier $id): User;

    public function findAll();

    public function addByEvent(UserWasCreated $event);

    public function updateByEvent(UserWasUpdated $event);
}
