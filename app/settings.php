<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder): void {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                "displayErrorDetails" => (string)getenv("APP_ENV") === "production", // Should be set to false in production
                "logError" => (bool)getenv("APP_DEBUG") === true,
                "logErrorDetails" => (bool)getenv("APP_DEBUG") === true,
                "logger" => [
                    "name" => (bool)getenv("APP_NAME") ? "slim" : (string)getenv("APP_NAME"),
                    "path" => "php://stdout",
                    "level" => ((bool)getenv("APP_DEBUG") === true ? Logger::DEBUG : Logger::ERROR),
                ],

                "doctrine" => [
                    "dev_mode" => true,
                    "cache_dir" => __DIR__ . "/../var/cache/doctrine",
                    "metadata_dirs" => [__DIR__ . "/../src/Infrastructure/Persistence/Doctrine/Mapping/"],
                    "connection" => [
                        "driver" => "pdo_pgsql",
                        "host" => getenv("DB_HOST"),
                        "port" => getenv("DB_PORT"),
                        "dbname" => getenv("DB_NAME"),
                        "user" => getenv("DB_USER"),
                        "password" => getenv("DB_PASSWORD"),
                    ],
                    "migrations" => [
                        "migrations_paths" => [
                            "App\\Infrastructure\\Persistence\\Doctrine\\Migrations" => __DIR__ . "/../src/Infrastructure/Persistence/Doctrine/Migrations",
                        ],
                    ],
                ],

                "twig" => [
                    // Template paths
                    "paths" => [
                        __DIR__ . "/../templates",
                    ],
                    // Twig environment options
                    "options" => [
                        // Should be set to true in production
                        "cache_enabled" => false,
                        "cache_path" => __DIR__ . "/../var/cache/twig",
                    ],
                ],
                "commands" => [
                    \App\Console\DataFixturesCommand::class,
                ],
            ]);
        },
    ]);
};
