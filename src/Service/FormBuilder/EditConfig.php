<?php

namespace Samynw\MaintenanceToolboxBundle\Service\FormBuilder;

use Samynw\MaintenanceToolboxBundle\Config\ToolboxConfig;
use Samynw\MaintenanceToolboxBundle\Form\ConfigType;
use Samynw\MaintenanceToolboxBundle\Tool\ArrayFormatter;

class EditConfig
{
    /** @var ToolboxConfig */
    private $config;

    /**
     * EditConfig constructor.
     * @param ToolboxConfig $config
     */
    public function __construct(ToolboxConfig $config)
    {
        $this->config = $config;
    }

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
        $formatter = new ArrayFormatter();
        return $formatter->toFlatArray($this->config->toArray());
    }
}
