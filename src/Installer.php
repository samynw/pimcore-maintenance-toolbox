<?php

namespace Samynw\MaintenanceToolboxBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Samynw\MaintenanceToolboxBundle\Config\ToolboxConfig;
use Pimcore\Db\ConnectionInterface;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\MigrationManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class Installer extends MigrationInstaller
{
    /** @var Filesystem */
    private $filesystem;
    /** @var FileLocator */
    private $fileLocator;

    /**
     * Installer constructor.
     * @param BundleInterface $bundle
     * @param ConnectionInterface $connection
     * @param MigrationManager $migrationManager
     * @param Filesystem $filesystem
     * @param FileLocator $fileLocator
     */
    public function __construct(
        BundleInterface $bundle,
        ConnectionInterface $connection,
        MigrationManager $migrationManager,
        FileSystem $filesystem,
        FileLocator $fileLocator
    ) {
        parent::__construct($bundle, $connection, $migrationManager);
        $this->filesystem = $filesystem;
        $this->fileLocator = $fileLocator;
    }

    /**
     * Install the bundle by creating the config file
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version): void
    {
        $source = $this->fileLocator->locate(
            '@MaintenanceToolboxBundle/Resources/data/' . ToolboxConfig::CONFIG_FILENAME . '.example'
        );
        $this->filesystem->copy($source, ToolboxConfig::getConfigFilePath(), false);

        // Verify the installation
        if (!\file_exists(ToolboxConfig::getConfigFilePath())) {
            throw new InstallationException(
                'Failed to create the config file. Please check your permissions in the var/config folder'
            );
        }
    }

    /**
     * Remove the config file to uninstall the bundle
     *
     * @param Schema $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version): void
    {
        $this->filesystem->remove(ToolboxConfig::getConfigFilePath());
    }

    /**
     * Bundle is installed if the config file exists
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        return \file_exists(ToolboxConfig::getConfigFilePath());
    }
}
