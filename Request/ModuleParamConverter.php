<?php

namespace Harmony\Bundle\ModularBundle\Request;

use Harmony\Component\ModularRouting\Provider\ProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * todo unfortunately we can't rely on getCurrentModule() because the onKernelController event
 * seems to fire up after this. For now we load the module again through the provider, but it would
 * be nice if the ModuleManager keeps track of the modules it has already loaded by default to
 * avoid multiple queries.
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class ModuleParamConverter implements ParamConverterInterface
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
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

        $object = $this->provider->loadModuleByRequest($request);
        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        return 'Harmony\Component\ModularRouting\Model\ModuleInterface' == $configuration->getClass();
    }
}
