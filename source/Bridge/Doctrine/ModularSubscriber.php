<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\Bridge\Doctrine;

use Doctrine\Common\EventSubscriber;
use Harmony\Component\ModularRouting\Bridge\Doctrine\EventListener\ModularSubscriber as BaseSubscriber;
use Harmony\Component\ModularRouting\Manager\ModuleManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * Handle events regarding modular entities.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class ModularSubscriber extends BaseSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param string             $moduleClass
     */
    public function __construct(ContainerInterface $container, $moduleClass)
    {
        $this->container   = $container;
        $this->moduleClass = $moduleClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleManager()
    {
        return $this->container->get(ModuleManagerInterface::class);
    }
}
