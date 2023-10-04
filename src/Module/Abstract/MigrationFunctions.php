<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;


use Vanengers\PrestashopLibModule\Module\Service\Migration\DatabaseMigrator;

trait MigrationFunctions
{
    public function getMigrator() : DatabaseMigrator
    {
        /** @var DatabaseMigrator $service */
        $service = $this->getService('vanengers.base_module.lib.migrator');
        $service->init($this);

        return $service;
    }
}