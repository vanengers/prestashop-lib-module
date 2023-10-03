<?php

namespace Vanengers\PrestashopLibModule\Module\Service\Installation;

use Db;
use Exception;
use Vanengers\PrestashopLibModule\Module\BaseModule;

abstract class AbstractInstaller
{
    /**
     * @param BaseModule $module
     * @return bool
     */
    abstract public function init(BaseModule $module): bool;
}