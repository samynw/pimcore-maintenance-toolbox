<?php

namespace MaintenanceToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class MaintenanceToolboxBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/maintenancetoolbox/js/pimcore/startup.js'
        ];
    }
}
