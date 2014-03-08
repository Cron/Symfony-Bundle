<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cron\CronBundle\Command;

use Cron\CronBundle\Entity\CronJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */ 
class CronListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:list')
            ->setDescription('List all available crons');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobs = $this->queryJobs();

        foreach ($jobs as $job) {
            $state = $job->getEnabled() ? 'x' : ' ';
            $output->writeln(sprintf(' [%s] %s', $state, $job->getName()));
        }
    }

    /**
     * @return CronJob[]
     */
    protected function queryJobs()
    {
        return $this->getContainer()->get('doctrine')->getRepository('CronCronBundle:CronJob')
            ->findBy(array(), array(
                    'name' => 'asc',
                ));
    }
}
