<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\DependencyInjection;

use Harmony\Component\ModularRouting\Bridge\Doctrine\Manager\ModuleManager as DoctrineModuleManager;
use Harmony\Component\ModularRouting\Manager\ModuleManagerInterface;
use Harmony\Component\ModularRouting\Manager\StaticModuleManager;
use Harmony\Component\ModularRouting\Provider\ProviderInterface;
use Harmony\Component\ModularRouting\Provider\SegmentProvider;
use Harmony\Component\ModularRouting\StaticModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads and manages the bundle configuration.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class HarmonyModularExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // Disable the bundle if no module class is specified
        if (!$config['module_class']) {
            return;
        }

        // Load services
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        // Set parameters
        $container->setParameter('harmony_modular.module_class', $config['module_class']);
        $container->setParameter('harmony_modular.module_identifier', $config['module_identifier']);

        $container->setParameter('harmony_modular.router.resource', $config['router']['resource']);
        $container->setParameter('harmony_modular.router.resource_type', $config['router']['resource_type'] ?: 'yaml');

        $this->setupRoutePrefix($config, $container);
        $this->setupServices($config, $container);
    }

    private function setupRoutePrefix(array $config, ContainerBuilder $container)
    {
        $config = $config['route_prefix'];

        $container->setParameter('harmony_modular.route_prefix.path', $config['prefix']);

        if ($config['defaults']) {
            $defaults = [];
            foreach ($config['defaults'] as $default) {
                $defaults[$default['name']] = $default['default'];
            }

            $container->setParameter('harmony_modular.route_prefix.defaults', $defaults);
        }
        else {
            $container->setParameter('harmony_modular.route_prefix.defaults', []);
        }

        if ($config['requirements']) {
            $requirements = [];
            foreach ($config['requirements'] as $requirement) {
                $requirements[$requirement['name']] = $requirement['requirement'];
            }

            $container->setParameter('harmony_modular.route_prefix.requirements', $requirements);
        }
        else {
            $container->setParameter('harmony_modular.route_prefix.requirements', []);
        }
    }

    private function setupServices(array $config, ContainerBuilder $container)
    {
        // Define a different default module manager when using the default StaticModule class
        // If you choose to extend the StaticModule class, make sure to change this manually
        $static = $config['module_class'] == StaticModule::class;
        $defaultManagerService = !$static ? DoctrineModuleManager::class : StaticModuleManager::class;

        // Set module manager service alias
        $managerService = $config['service']['module_manager'] ?: $defaultManagerService;

        $container->setAlias(ModuleManagerInterface::class, $managerService);

        // Set provider service alias
        $providerService = $config['service']['provider'] ?: SegmentProvider::class;

        $container->setAlias(ProviderInterface::class, $providerService);
    }
}
