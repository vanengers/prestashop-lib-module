<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;

use Exception;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
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
        $installer = $this->getService('vanengers.base_module.lib.installer');
        if (empty($installer)) {
            $installer = new Installer();
        }

        return parent::install() && $installer->init($this);
    }

    /**
     * @throws Exception
     */
    public function uninstall(): bool
    {
        $this->preLoad();

        /** @var Uninstaller $unInstaller */
        $unInstaller = $this->getService('vanengers.base_module.lib.uninstaller');
        if (empty($installer)) {
            $unInstaller = new Uninstaller();
        }

        return parent::uninstall() && $unInstaller->init($this);
    }
}