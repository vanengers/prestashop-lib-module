<?php

namespace Vanengers\PrestashopLibModule\Module\Abstract;

use Context;
use Exception;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Throwable;
use Tools;

trait ContainerFunctions
{
    /**
     * @return void
     */
    private function autoLoad(): void
    {
        $autoLoadPath = $this->getLocalPath().'vendor/autoload.php';
        require_once $autoLoadPath;
    }

    /**
     * @param $serviceName
     * @throws Exception
     * @since 11-09-2023
     * @author George van Engers <george@dewebsmid.nl>
     */
    public function getService($serviceName)
    {
        $result = false;
        try {
            if (method_exists($this, 'get')) {
                $result = $this->get($serviceName);
            }
            if (!$result && Context::getContext()->controller) {
                if (property_exists(Context::getContext()->controller, 'get')) {
                    $controller = Context::getContext()->controller;
                    $result = $controller->get($serviceName);
                }
            }
            if (!$result) {
                $container = $this->getAwareContainer();
                $result = $container->get($serviceName);
            }
        } catch (Throwable) {
            // check fallback? -> baseService executing new baseService derived from this trait
            if (property_exists($this, 'api') && property_exists($this, 'logger')) {
                try {
                    $result = new $serviceName($this->api, $this->logger);
                }
                catch (Throwable) {}
            }
        }
        finally {
            if (Tools::isSubmit('debug') && Tools::isSubmit('debug_di')) {
                dump([$serviceName, $result != false]);
            }
            return $result;
        }
    }

    private ContainerInterface|null $containerAware = null;

    /**
     * @return ContainerInterface
     * @throws ContainerNotFoundException
     * @since 11-09-2023
     * @author George van Engers <george@dewebsmid.nl>
     */
    public function getAwareContainer(): ContainerInterface
    {
        if (null === $this->containerAware) {
            $finder = new ContainerFinder(Context::getContext());
            $this->containerAware = $finder->getContainer();
        }

        return $this->containerAware;
    }
}