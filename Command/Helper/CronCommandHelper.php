<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cron\CronBundle\Command\Helper;

use Cron\CronBundle\Cron\ManagerDecorator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

abstract class CronCommandHelper
{
    public static function setManagerHelper(Application $application, InputInterface $input)
    {            
        $connection = $application->getKernel()->getContainer()->get('doctrine')->getConnection($input->getOption('connection'));

        $em = $application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $em = $em->create($connection, $em->getConfiguration());

        $cronManager = new ManagerDecorator($application->getKernel()->getContainer()->get('cron.manager'), $em);
        
        return $cronManager;
    }
}