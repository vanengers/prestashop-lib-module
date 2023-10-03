<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

trait ContainerFunctions
{
    /**
     * @var Container
     */
    private Container $moduleContainer;

    /**
     * @return void
     */
    private function autoLoad(): void
    {
        $autoLoadPath = $this->getLocalPath().'vendor/autoload.php';
        require_once $autoLoadPath;
    }

    /**
     * @return Container
     */
    public function getContainer() : Container
    {
        return $this->moduleContainer;
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function compile(): void
    {
        $containerCache = $this->getLocalPath().'var/cache/container.php';
        $containerConfigCache = new ConfigCache($containerCache, _PS_MODE_DEV_);

        $containerClass = get_class($this).'Container';

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $locator = new FileLocator(dirname(__FILE__).'/config');
            $loader  = new YamlFileLoader($containerBuilder, $locator);
            $loader->load('local.yml');
            $containerBuilder->compile();
            $dumper = new PhpDumper($containerBuilder);

            $containerConfigCache->write(
                $dumper->dump(['class' => $containerClass]),
                $containerBuilder->getResources()
            );
        }

        require_once $containerCache;
        $this->moduleContainer = new $containerClass();
    }
}