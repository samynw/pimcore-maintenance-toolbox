<?php

namespace MaintenanceToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class MaintenanceToolboxBundle extends AbstractPimcoreBundle
{
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
