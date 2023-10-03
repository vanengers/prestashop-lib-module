<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;

use ReflectionClass;
use Vanengers\PrestashopLibModule\Module\Abstract\Configuration\ModuleConfigurationList;
use Vanengers\PrestashopLibModule\Module\Abstract\Hook\ModuleHookList;

trait ModuleInplementation
{
    /** ---------------------------------------------------------------------------------------------------------- */
    /** Abstractions implementable by Module itself */

    /**
     * We assign the hooks here. By using:
     * $this->getHooks()->addHook('displayHeader'); or
     * $this->getHooks()->addHook(['displayFooter','displayTop']); etc.
     * WE load ALL available HOOKS from className with Reflection, so no need to add manually.
     * @return void
     */
    //public abstract function preloadAssignHooks() : void;

    /**
     * We assign configurations here by using:
     * $this->getConfiguration()->addConfiguration('MYMODULE_NAME', 'MYMODULE_VALUE', [1,2,3], true); etc.
     * or by using: $this->getConfiguration()->addConfigurations([['MYMODULE_NAME', 'MYMODULE_VALUE', [1,2,3], true]]);
     * @return void
     */
    public abstract function preloadAssignConfigurations() : void;

    /** ---------------------------------------------------------------------------------------------------------- */

    /** @var ModuleHookList $hooks */
    private ModuleHookList $hooks;

    /**
     * @return ModuleHookList
     */
    public function getHooks() : ModuleHookList
    {
        if ($this->hooks == null) {
            $this->hooks = new ModuleHookList();
        }

        return $this->hooks;
    }

    /** @var ModuleConfigurationList $configuration */
    private ModuleConfigurationList $configuration;

    /**
     * @return ModuleConfigurationList
     */
    public function getConfiguration() : ModuleConfigurationList
    {
        if ($this->configuration == null) {
            $this->configuration = new ModuleConfigurationList();
        }

        return $this->configuration;
    }

    private bool $preLoaded = false;

    /**
     * @return void
     */
    private function preLoad(): void
    {
        if (!$this->preLoaded) {
            $this->preloadAssignHooks();
            $this->preloadAssignConfigurations();
            $this->preLoaded = true;
        }
    }

    /**
     * Try to autoload all available hook methods. We then allways assure the hooks exist.
     * No hooks that can't be executed will be added.
     * This will only be executed ONCE at installation.
     * All hooks from all used Traits will be added in here.
     * @return void
     */
    private function preloadAssignHooks()
    {
        // we get all methods from Reflection by prefix "hook"
        $class = new ReflectionClass(get_class($this));
        $methods = $class->getMethods();
        foreach($methods as $method) {
            if (str_starts_with($method->name, 'hook')) {
                $hookName = str_replace('hook', '', $method->name);
                $this->getHooks()->addHook($hookName);
            }
        }
    }
}