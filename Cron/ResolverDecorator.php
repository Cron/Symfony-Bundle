<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cron\CronBundle\Cron;

use Cron\CronBundle\Cron\ManagerDecorator;
use Cron\Resolver\ResolverInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class ResolverDecorator extends Resolver
{
    /**
     * @param RegistryInterface $registry
     */
    function __construct(ResolverInterface $resolver, ManagerDecorator $manager)
    {
        $this->manager = $manager;
        $this->commandBuilder = $resolver->commandBuilder;
        $this->rootDir = $resolver->rootDir;
    }
}