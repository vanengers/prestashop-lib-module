<?php

namespace Vanengers\PrestashopLibModule\Module\Service\Installation;

use Db;
use Exception;
use Module;
use Vanengers\PrestashopLibModule\Module\BaseModule;

abstract class AbstractInstaller
{
    /**
     * @param Module $module
     * @return bool
     */
    abstract public function init(Module $module): bool;
}