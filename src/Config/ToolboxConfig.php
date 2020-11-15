<?php

namespace MaintenanceToolboxBundle\Config;

use Pimcore\Config;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ToolboxConfig
{
    public const CONFIG_FILENAME = 'maintenance-toolbox.yml';
    public const FEATURE_RELEASE = 'release';

    /** @var array */
    private $config;

    public function __construct(string $configPath = null)
    {
        $this->loadConfigFile($configPath);
    }

    /**
     * Parse the config file for this bundle
     * @param string|null $configFile
     */
    private function loadConfigFile(string $configFile = null): void
    {
        try {
            if ($configFile === null) {
                $configFile = Config::locateConfigFile(self::CONFIG_FILENAME);
            }
            $config = Yaml::parseFile($configFile);
            $this->config = $config['maintenancetoolbox'];
        } catch (ParseException $e) {
            // Config file is not found, use empty array as blank config
            $this->config = [];
        }
    }

    /**
     * Check the "enabled" setting of a feature in the config
     *
     * @param string $feature
     * @return bool
     */
    public function isFeatureEnabled(string $feature): bool
    {
        if (\array_key_exists($feature, $this->config)) {
            return $this->config[$feature]['enabled'];
        }

        return false;
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

    /**
     * Return the config as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * Write config to file
     *
     * @param array $data
     */
    public function save(array $data): void
    {
        $yaml = Yaml::dump(['maintenancetoolbox' => $data], 5);
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile(self::getConfigFilePath(), $yaml);

        // Reload
        $this->loadConfigFile();
    }
}
