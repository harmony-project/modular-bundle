<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\Request;

use Harmony\Component\ModularRouting\Manager\ModuleManagerInterface;
use Harmony\Component\ModularRouting\ModuleInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Match the module argument of the controller.
 *
 * @author Tim Goudriaan <tim@harmony-project.io>
 */
class ModuleParamConverter implements ParamConverterInterface
{
    /**
     * @var ModuleManagerInterface
     */
    protected $manager;

    /**
     * @param ModuleManagerInterface $manager
     */
    public function __construct(ModuleManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If one of the parameters has an invalid value
     * @throws ResourceNotFoundException If no module was matched to the request
     */
    public function apply(Request $request, ParamConverterConfiguration $configuration)
    {
        $name = $configuration->getName();

        $object = $this->manager->getCurrentModule();
        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        return $configuration->getClass() == ModuleInterface::class;
    }
}
