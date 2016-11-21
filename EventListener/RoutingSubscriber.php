<?php

namespace Harmony\Bundle\ModularBundle\EventListener;

use Harmony\Component\ModularRouting\EventListener\RoutingSubscriber as BaseSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * RoutingSubscriber
 *
 * Handle events regarding routing.
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class RoutingSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container Service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getModuleManager()
    {
        return $this->container->get('harmony_modular.module_manager');
    }

    public function getModularRouter()
    {
        return $this->container->get('harmony_modular.router');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
