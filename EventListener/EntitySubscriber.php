<?php

namespace Harmony\Bundle\ModularBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Harmony\Component\ModularRouting\EventListener\EntitySubscriber as BaseSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EntitySubscriber
 *
 * Handle events regarding modular entities.
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class EntitySubscriber extends BaseSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container   Service container
     * @param string             $moduleClass
     */
    public function __construct(ContainerInterface $container, $moduleClass)
    {
        $this->container   = $container;
        $this->moduleClass = $moduleClass;
    }
    
    public function getModuleManager()
    {
        return $this->container->get('harmony_modular.module_manager');
    }
}
