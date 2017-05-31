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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use Harmony\Component\ModularRouting\Manager\ModuleManagerInterface;
use Harmony\Component\ModularRouting\Model\ModularRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Match the any modular (entity) argument of the controller
 *
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class ModularParamConverter extends DoctrineParamConverter implements ParamConverterInterface
{
    /**
     * @var ModuleManagerInterface
     */
    protected $manager;

    public function __construct(ManagerRegistry $registry = null, ModuleManagerInterface $manager)
    {
        $this->manager = $manager;

        parent::__construct($registry);
    }

    protected function findOneBy($class, Request $request, $options)
    {
        if (!$options['mapping']) {
            $keys = $request->attributes->keys();
            $options['mapping'] = $keys ? array_combine($keys, $keys) : array();
        }

        foreach ($options['exclude'] as $exclude) {
            unset($options['mapping'][$exclude]);
        }

        if (!$options['mapping']) {
            return false;
        }

        // if a specific id has been defined in the options and there is no corresponding attribute
        // return false in order to avoid a fallback to the id which might be of another object
        if (isset($options['id']) && null === $request->attributes->get($options['id'])) {
            return false;
        }

        $criteria = array();
        $em = $this->getManager($options['entity_manager'], $class);
        $metadata = $em->getClassMetadata($class);

        $mapMethodSignature = isset($options['repository_method'])
            && isset($options['map_method_signature'])
            && $options['map_method_signature'] === true;

        foreach ($options['mapping'] as $attribute => $field) {
            if ($metadata->hasField($field)
                || ($metadata->hasAssociation($field) && $metadata->isSingleValuedAssociation($field))
                || $mapMethodSignature) {
                $criteria[$field] = $request->attributes->get($attribute);
            }
        }

        if ($options['strip_null']) {
            $criteria = array_filter($criteria, function ($value) { return !is_null($value); });
        }

        if (!$criteria) {
            return false;
        }

        // Apply the correct module for the query
        $criteria['module'] = $this->manager->getCurrentModule();

        if (isset($options['repository_method'])) {
            $repositoryMethod = $options['repository_method'];
        } else {
            $repositoryMethod = 'findOneBy';
        }

        if ($mapMethodSignature) {
            throw new \RuntimeException('map_method_signature is not supported by ModularBundle.');
        }

        try {
            return $em->getRepository($class)->$repositoryMethod($criteria);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        if (!parent::supports($configuration)) {
            return false;
        }

        $options = $this->getOptions($configuration);

        $em         = $this->getManager($options['entity_manager'], $configuration->getClass());
        $repository = $em->getRepository($configuration->getClass());

        if (!$repository instanceof ModularRepositoryInterface) {
            return false;
        }

        $metadata = $em->getClassMetadata($configuration->getClass());

        return $metadata->hasField('module');
    }

    private function getManager($name, $class)
    {
        if (null === $name) {
            return $this->registry->getManagerForClass($class);
        }

        return $this->registry->getManager($name);
    }
}
