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

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class ManagerDecorator extends Manager
{
    /**
     * @param RegistryInterface $registry
     */
    function __construct(Manager $manager, EntityManager $em)
    {
        $this->manager = $em;
    }
}