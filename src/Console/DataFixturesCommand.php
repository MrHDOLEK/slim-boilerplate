<?php

declare(strict_types=1);

namespace App\Console;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataFixturesCommand extends Command
{
    protected static $defaultName = "db:seed";

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Purge db");

        $em = $this->entityManager;
        $platform = $em->getConnection()->getDatabasePlatform();
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        foreach ($metadata as $metadata) {
            if (!$metadata->isMappedSuperclass) {
                $tbl = $metadata->getSequencePrefix($platform);
                $em->getConnection()->executeStatement("ALTER SEQUENCE " . $tbl . "_id_seq" . " RESTART;");
            }
        }

        $loader = new Loader();
        $loader->loadFromDirectory(dirname(__DIR__, 2) . "/src/Infrastructure/Persistence/Doctrine/Fixtures");
        $executor = new ORMExecutor($em, $purger);
        /** @phpstan-ignore-next-line  */
        $executor->execute($loader->getFixtures());
        $output->writeln("Completed load fixtures to db");

        return 0;
    }
}
