<?php

declare(strict_types = 1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use DI\Container;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/** @var Container $container */
$container = require_once __DIR__ . "/app/bootstrap.php";

$factory = DependencyFactory::fromEntityManager(
    $container->get(ConfigurationLoader::class),
    new ExistingEntityManager($container->get(EntityManagerInterface::class))
);

ConsoleRunner::run(
    ConsoleRunner::createHelperSet($container->get(EntityManagerInterface::class)),
    [
        new Doctrine\Migrations\Tools\Console\Command\ExecuteCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\GenerateCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\LatestCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\MigrateCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\DiffCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\UpToDateCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\StatusCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\VersionCommand($factory),
        new Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand($factory)
    ]
);
