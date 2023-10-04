<?php

namespace Vanengers\PrestashopLibModule\Module\Service\Installation;

use Configuration;
use Db;
use Exception;
use Module;
use Symfony\Component\Yaml\Yaml;
use Vanengers\PrestashopLibModule\Module\Service\Migration\DatabaseMigrator;

class Installer extends AbstractInstaller
{
    /**
     * @var Module
     */
    private Module $module;

    /**
     * @param Module $module
     * @return bool
     * @throws \ReflectionException
     */
    public function init(Module $module) : bool
    {
        $this->module = $module;

        if (!$this->registerHooks()) {
            return false;
        }

        if (!$this->installConfiguration()) {
            return false;
        }

        if (!$this->installDb()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     * @throws Exception
     */
    private function registerHooks() : bool
    {
        $hooks = $this->module->getHooks()->getHooks();

        if (empty($hooks)) {
            return true;
        }

        foreach ($hooks as $hookName) {
            if (!$this->module->registerHook($hookName)) {
                throw new Exception(
                    sprintf(
                        $this->module->l('Hook %s has not been installed.', $this->getFileName($this)),
                        $hookName
                    )
                );
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     * @throws Exception
     */
    private function installConfiguration() : bool
    {
        $configurations = $this->module->getConfiguration();

        if (!$configurations->hasConfigurations()) {
            return true;
        }

        foreach ($configurations->getConfigurations() as $conf) {
            $shops = count($conf->shops) > 0 ? $conf->shops : [null];
            foreach($shops as $id_shop) {
                if ($conf->override || !Configuration::getIdByName($conf->name,null, $id_shop)) {
                    if (!Configuration::updateValue($conf->name, $conf->value, true, null, $id_shop)) {
                        throw new Exception(
                            sprintf(
                                $this->module->l('Configuration %s has not been installed.', $this->getFileName($this)),
                                $conf->name
                            )
                        );
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function installDb() : bool
    {
        $migrator = new DatabaseMigrator();
        $migrator->init($this->module);

        return $migrator->migrateUp();
    }
}