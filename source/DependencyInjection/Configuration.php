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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class validates and merges configuration for the bundle.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $root = $treeBuilder->root('harmony_modular');

        $root
            ->children()
                ->scalarNode('module_class')->defaultNull()->end()
                ->scalarNode('module_identifier')->defaultValue('id')->end()
            ->end()
        ;

        $this->addRoutePrefixSection($root);
        $this->addRouterSection($root);
        $this->addServicesSection($root);

        return $treeBuilder;
    }

    private function addRoutePrefixSection(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('route_prefix')
                    ->info('route prefix configuration')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()
                        ->ifString()
                            ->then(function($value) { return [
                                'prefix'       => $value,
                                'defaults'     => [],
                                'requirements' => [],
                            ]; })
                    ->end()
                    ->children()
                        ->scalarNode('prefix')->defaultValue('/module')->end()
                        ->arrayNode('defaults')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('default')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('requirements')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('requirement')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addRouterSection(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('router')
                    ->info('router configuration')
                    ->children()
                    ->scalarNode('resource')->isRequired()->end()
                    ->scalarNode('resource_type')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addServicesSection(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('service')
                    ->info('services configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('module_manager')->defaultNull()->end()
                        ->scalarNode('provider')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
