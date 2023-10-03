<?php

namespace Vanengers\PrestashopLibModule\Module\Service\Installation;

use Configuration;
use Db;
use Exception;
use Vanengers\PrestashopLibModule\Module\BaseModule;
use Vanengers\PrestashopLibModule\Module\Service\Migration\DatabaseMigrator;

class Uninstaller extends AbstractInstaller
{
    /**
     * @var BaseModule
     */
    private BaseModule $module;

    /**
     * @param BaseModule $module
     * @return bool
     * @throws Exception
     */
    public function init(BaseModule $module) : bool
    {
        $this->module = $module;

        $this->uninstallConfiguration();

        if (!$this->uninstallDb()) {
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    private function uninstallConfiguration() : void
    {
        $configurations = $this->module->getConfiguration();

        if (!$configurations->hasConfigurations()) {
            return;
        }

        foreach ($configurations->getConfigurations() as $conf) {
            Configuration::deleteByName($conf->name);
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function uninstallDb() : bool
    {
        $migrator = new DatabaseMigrator();
        $migrator->init($this->module);
        return $migrator->migrateDown();
    }
}