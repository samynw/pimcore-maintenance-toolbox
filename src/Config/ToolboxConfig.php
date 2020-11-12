<?php

namespace MaintenanceToolboxBundle\Config;

use Pimcore\Config;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ToolboxConfig
{
    public const CONFIG_FILENAME = 'maintenance-toolbox.yml';
    public const FEATURE_RELEASE = 'release';

    /** @var array */
    private $config;

    public function __construct()
    {
        $this->loadConfigFile();
    }

    /**
     * Parse the config file for this bundle
     */
    private function loadConfigFile(): void
    {
        try {
            $configFile = Config::locateConfigFile(self::CONFIG_FILENAME);
            $config = Yaml::parseFile($configFile);
            $this->config = $config['maintenancetoolbox'];
        }catch(ParseException $e){
            // Config file is not found, use empty array as blank config
            $this->config = [];
        }
    }

    /**
     * Return the location of where the config file is located (or should be located)
     *
     * @return string
     */
    public static function getConfigFilePath(): string
    {
        return \PIMCORE_CONFIGURATION_DIRECTORY . '/' . self::CONFIG_FILENAME;
    }
}
