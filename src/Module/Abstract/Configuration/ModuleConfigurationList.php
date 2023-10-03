<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract\Configuration;

use Validate;

class ModuleConfigurationList
{
    /** @var ConfigurationItem[] $configurations */
    private array $configurations = [];

    /**
     * @param string $name
     * @param mixed $value
     * @param array $shops
     * @param bool $override
     * @return void
     */
    public function addConfiguration(string $name, mixed $value, array $shops = [], bool $override = true) : void
    {
        if (Validate::isConfigName($name)) {
            $this->configurations[] = new ConfigurationItem($name, $value, $shops, $override);
        }
    }

    /**
     * @param array $configurations
     * @return void
     */
    public function addConfigurations(array $configurations) : void
    {
        foreach ($configurations as $configuration) {
            if (array_key_exists('name', $configuration) && array_key_exists('value', $configuration)) {
                $shops = array_key_exists('shops', $configuration) ? $configuration['shops'] : [];
                $override = array_key_exists('override', $configuration) ? $configuration['override'] : true;
                $this->addConfiguration($configuration['name'], $configuration['value'], $shops, $override);
            }

        }
    }

    /**
     * @return ConfigurationItem[]
     */
    public function getConfigurations() : array
    {
        return $this->configurations;
    }

    /**
     * @return bool
     */
    public function hasConfigurations() : bool
    {
        return !empty($this->configurations);
    }
}