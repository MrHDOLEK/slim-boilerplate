<?php

declare(strict_types=1);

namespace App\Domain\Entity\User;

use App\Domain\Entity\User\Exception\UserNotFoundException;

interface UserRepositoryInterface
{
    /**
     * @return array<User>
     */
    public function findAll(): array;

    /**
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;
}
