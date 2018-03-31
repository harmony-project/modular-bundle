<?php

namespace Harmony\Bundle\ModularBundle\EventListener;

use Harmony\Component\ModularRouting\EventListener\RoutingSubscriber as BaseSubscriber;
use Harmony\Component\ModularRouting\Manager\ModuleManagerInterface;
use Harmony\Component\ModularRouting\ModularRouter;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Handles events regarding modular routing.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class RoutingSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleManager()
    {
        return $this->container->get(ModuleManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getModularRouter()
    {
        return $this->container->get(ModularRouter::class);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 16]],
        ];
    }
}
