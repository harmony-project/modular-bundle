<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Harmony\Component\ModularRouting\Doctrine\ModularSubscriber as BaseSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handle events regarding modular entities.
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class ModularSubscriber extends BaseSubscriber implements EventSubscriber
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
