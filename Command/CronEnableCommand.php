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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronEnableCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:enable')
            ->setDescription('Enable a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to enable');
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $this->queryJob($input->getArgument('job'));

        if (!$job) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        $job->setEnabled(true);

        $this->getContainer()->get('cron.manager')
            ->saveJob($job);

        $output->writeln(sprintf('Cron "%s" enabled', $job->getName()));

        return 0;
    }

    /**
     * @param  string  $jobName
     * @return CronJob
     */
    protected function queryJob($jobName)
    {
        return $this->getContainer()->get('cron.manager')
            ->getJobByName($jobName);
    }
}
