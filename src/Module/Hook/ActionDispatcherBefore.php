<?php

namespace Vanengers\PrestashopLibModule\Module\Hook;

Trait ActionDispatcherBefore
{
    /**
     * Just to be sure that the autoloader is loaded
     * @return void
     */
    public function hookActionDispatcherBefore()
    {
        $this->autoLoad();
    }
}