<?php

namespace Samynw\MaintenanceToolboxBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Samynw\MaintenanceToolboxBundle\Config\ToolboxConfig;
use Pimcore\Db\ConnectionInterface;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Migrations\MigrationManager;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class Installer extends AbstractInstaller
{
    /** @var Filesystem */
    private $filesystem;
    /** @var FileLocator */
    private $fileLocator;

    /**
     * Installer constructor.
     * @param Filesystem $filesystem
     * @param FileLocator $fileLocator
     */
    public function __construct(
        FileSystem $filesystem,
        FileLocator $fileLocator
    )
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->fileLocator = $fileLocator;
    }

    /**
     * Install the bundle by creating the config file
     */
    public function install(): void
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
     */
    public function uninstall(): void
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

    /**
     * No specific requirement, just check if it's not installed yet
     *
     * @return bool
     */
    public function canBeInstalled(): bool
    {
        return !$this->isInstalled();
    }

    /**
     * No specific requirement, just check if it's already installed
     *
     * @return bool
     */
    public function canBeUninstalled():bool
    {
        return $this->isInstalled();
    }


}
