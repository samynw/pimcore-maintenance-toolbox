<?php

namespace Samynw\MaintenanceToolboxBundle\Tests\app;

use Samynw\MaintenanceToolboxBundle\MaintenanceToolboxBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{

    public function registerBundles()
    {
        return [
            new MaintenanceToolboxBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // TODO: Implement registerContainerConfiguration() method.
    }
}
