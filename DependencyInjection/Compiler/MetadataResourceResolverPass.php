<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Scans the services tree for resource loaders and adds them to the resource resolver.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class MetadataResourceResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('harmony_modular.metadata.resource_resolver')) {
            return;
        }
        
        $definition = $container->getDefinition('harmony_modular.metadata.resource_resolver');
        
        foreach ($container->findTaggedServiceIds('harmony_modular.metadata.resource_loader') as $id => $attributes) {
            $definition->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}
