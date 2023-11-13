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
class CronDisableCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cron:disable')
            ->setDescription('Disable a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to disable');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = $this->queryJob($input->getArgument('job'));

        if (!$job) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        $job->setEnabled(false);

        $this->getContainer()->get('cron.manager')
            ->saveJob($job);

        $output->writeln(sprintf('Cron "%s" disabled', $job->getName()));

        return 0;
    }

    protected function queryJob(string $jobName): CronJob
    {
        return $this->getContainer()->get('cron.manager')
            ->getJobByName($jobName);
    }
}
