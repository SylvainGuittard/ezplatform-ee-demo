<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace AppBundle\Installer;

use EzSystems\PlatformInstallerBundle\Installer\CleanInstaller;
use Symfony\Component\Filesystem\Filesystem;

class PlatformEEDemoInstaller extends CleanInstaller
{
    use InstallerCommandExecuteTrait;

    public function importSchema()
    {
        parent::importSchema();
    }

    public function importData()
    {
        parent::importData();

        try {
            $this->db->exec('TRUNCATE TABLE kaliop_migrations;');
            $this->db->exec('TRUNCATE TABLE kaliop_migrations_contexts;');
        } catch (\Exception $e) {
        }

        $migrationCommands = [
            'cache:clear',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/schema_mysql.sql -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/schema-ee_mysql.sql -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/tags.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/product_list.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/all.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/users.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/landing_page_contenttype.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/landing_page.yml -n',
            'kaliop:migration:migrate --path=src/AppBundle/MigrationVersions/cleanup-ee.yml -n --no-transactions',
        ];

        foreach ($migrationCommands as $cmd) {
            $this->output->writeln(sprintf('executing migration: %s', $cmd));
            $this->executeCommand($this->output, $cmd, 0);
        }
    }

    public function importBinaries()
    {
        $this->output->writeln('Copying storage directory contents...');
        $fs = new Filesystem();
        $fs->mkdir('web/var/site');
//        $fs->mirror(
//            'vendor/ezsystems/ezstudio-demo-bundle-data/data/storage',
//            'web/var/site/storage'
//        );
    }
}
