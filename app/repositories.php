<?php

declare(strict_types=1);

use App\Domain\Entity\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Repository\UserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder): void {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepositoryInterface::class => \DI\autowire(UserRepository::class),
    ]);
};
