<?php

namespace Vanengers\PrestashopLibModule;

use Vanengers\PrestashopLibModule\Module\Abstract\ContainerFunctions;
use Vanengers\PrestashopLibModule\Module\Abstract\InstallationFunctions;
use Vanengers\PrestashopLibModule\Module\Abstract\MigrationFunctions;
use Vanengers\PrestashopLibModule\Module\Abstract\ModuleInplementation;
use Vanengers\PrestashopLibModule\Module\Hook\ActionDispatcherBefore;

Trait VanengersBaseFunctions
{
    /** HOOKS */
    use ActionDispatcherBefore;

    /** Abstract implementations for extendable class (module) */
    use ModuleInplementation;

    /** Installation functions */
    use InstallationFunctions;

    /** Container functions */
    use ContainerFunctions;

    /** Database Migration functions for upgrading */
    use MigrationFunctions;
}