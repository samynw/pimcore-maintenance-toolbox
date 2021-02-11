<?php

namespace MaintenanceToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class MaintenanceToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * The human readable name of the bundle
     *
     * @return string
     */
    public function getNiceName()
    {
        return 'Maintenance Toolbox';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    public function getAdminIframePath(): string
    {
        return '/admin/maintenance-toolbox/config';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'samynw/pimcore-maintenance-toolbox';
    }
}
