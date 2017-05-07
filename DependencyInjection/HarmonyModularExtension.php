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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads and manages the bundle configuration.
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class HarmonyModularExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // disable the bundle if no module class is specified
        if (!$config['module_class']) {
            return;
        }

        // Load services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

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
        $static = 'Harmony\Component\ModularRouting\Model\StaticModule' == $config['module_class'];
        $defaultManagerService = !$static ? 'harmony_modular.module_manager.doctrine' : 'harmony_modular.module_manager.static';

        // Set module manager service alias
        $managerService = $config['service']['module_manager'] ?: $defaultManagerService;

        $container->setAlias('harmony_modular.module_manager', $managerService);

        // Set provider service alias
        $providerService = $config['service']['provider'] ?: 'harmony_modular.provider.segment';

        $container->setAlias('harmony_modular.provider', $providerService);
    }
}
