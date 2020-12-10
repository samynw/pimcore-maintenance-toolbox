<?php

namespace MaintenanceToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class MaintenanceToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

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
