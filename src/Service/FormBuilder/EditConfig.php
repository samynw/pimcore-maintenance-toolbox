<?php

namespace MaintenanceToolboxBundle\Service\FormBuilder;

use MaintenanceToolboxBundle\Config\ToolboxConfig;
use MaintenanceToolboxBundle\Form\ConfigType;
use MaintenanceToolboxBundle\Tool\ArrayFormatter;

class EditConfig
{
    /**
     * Return FQCN of the form to build
     *
     * @return string
     */
    public function getFormClassName(): string
    {
        return ConfigType::class;
    }

    /**
     * The default options for the form
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return [
            'csrf_protection' => false,
        ];
    }

    /**
     * Get the default values from the config file
     *
     * @return array
     */
    public function getDefaultValues(): array
    {
        $currentConfig = new ToolboxConfig();
        $formatter = new ArrayFormatter();
        return $formatter->toFlatArray($currentConfig->toArray());
    }
}
