<?php
/*
 * This file is part of the Harmony package.
 *
 * (c) Tim Goudriaan <tim@harmony-project.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Harmony\Bundle\ModularBundle\Twig;

use Harmony\Component\ModularRouting\Manager\ModuleManager;

/**
 * @author Tim Goudriaan <tim@codedmonkey.com>
 */
class ModularExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var ModuleManager
     */
    private $manager;

    /**
     * @param ModuleManager $manager
     */
    public function __construct(ModuleManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'module' => $this->manager->getCurrentModule(),
        ];
    }
}
