<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;

use Exception;
use Vanengers\PrestashopLibModule\Module\Service\Installation\Installer;
use Vanengers\PrestashopLibModule\Module\Service\Installation\Uninstaller;

trait InstallationFunctions
{
    /**
     * @return bool
     * @throws Exception
     */
    public function install(): bool
    {
        $this->preLoad();

        /** @var Installer $installer */
        $installer = $this->getContainer()->get('vanengers.base_module.lib.installer');

        return parent::install() && $installer->init($this);
    }

    /**
     * @throws Exception
     */
    public function uninstall(): bool
    {
        $this->preLoad();

        /** @var Uninstaller $unInstaller */
        $unInstaller = $this->getContainer()->get('vanengers.base_module.lib.uninstaller');

        return parent::uninstall() && $unInstaller->init($this);
    }
}