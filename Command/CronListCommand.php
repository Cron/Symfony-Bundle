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

use Cron\CronBundle\Cron\CronCommand;
use Cron\CronBundle\Entity\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronListCommand extends CronCommand
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobs = $this->queryJobs();

        foreach ($jobs as $job) {
            $state = $job->getEnabled() ? 'x' : ' ';
            $output->writeln(sprintf(' [%s] %s', $state, $job->getName()));
        }

        return 0;
    }

    /**
     * @return CronJob[]
     */
    protected function queryJobs()
    {
        return $this->getContainer()->get('cron.manager')->listJobs();
    }
}
