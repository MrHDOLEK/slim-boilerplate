<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Tools\Setup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Symfony\Component\Console\Application;

return function (ContainerBuilder $containerBuilder): void {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get("logger");
            $logger = new Logger($loggerSettings["name"]);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings["path"], $loggerSettings["level"]);
            $logger->pushHandler($handler);

            return $logger;
        },

        Application::class => function (ContainerInterface $container): Application {
            $application = new Application();

            foreach ($container->get(SettingsInterface::class)->get("commands") as $class) {
                $application->add($container->get($class));
            }

            return $application;
        },

        EntityManagerInterface::class => function (ContainerInterface $container): EntityManager {
            $doctrineSettings = $container->get(SettingsInterface::class)->get("doctrine");

            $config = Setup::createAnnotationMetadataConfiguration(
                $doctrineSettings["metadata_dirs"],
                $doctrineSettings["dev_mode"],
            );

            $config->setMetadataDriverImpl(
                new SimplifiedXmlDriver(
                    [
                        $doctrineSettings["metadata_dirs"][0] => 'App\Domain',
                    ],
                ),
            );

            return EntityManager::create($doctrineSettings["connection"], $config);
        },

        ConfigurationLoader::class => static function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class)->get("doctrine")["migrations"];

            return new ConfigurationArray($settings);
        },
        Twig::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);
            $twigSettings = $settings->get("twig");

            $options = $twigSettings["options"];
            $options["cache"] = $options["cache_enabled"] ? $options["cache_path"] : false;

            return Twig::create($twigSettings["paths"], $options);
        },
    ]);
};
