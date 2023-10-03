<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract\Hook;

use Validate;

class ModuleHookList
{
    /** @var string[] $hooks */
    private array $hooks = [];

    public function addHook(string $name) : void
    {
        if (Validate::isHookName($name)) {
            $this->hooks[] = $name;
        }
    }

    public function getHooks() : array
    {
        return $this->hooks;
    }
}