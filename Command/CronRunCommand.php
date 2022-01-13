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

use Cron\Cron;
use Cron\CronBundle\Cron\CronCommand;
use Cron\CronBundle\Entity\CronJob;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronRunCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:run')
            ->setDescription('Runs any currently schedule cron jobs')
            ->addArgument('job', InputArgument::OPTIONAL, 'Run only this job (if enabled)')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force schedule the current job.')
            ->addOption('schedule_now', null, InputOption::VALUE_NONE, 'Temporary set the job schedule to now.')
            ->addOption('script-name', null, InputOption::VALUE_OPTIONAL, 'Specify this to avoid guessing the script name to run the command in non-CLI context.');
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cron = new Cron();
        $cron->setExecutor($this->getContainer()->get('cron.executor'));
        if ($input->getArgument('job')) {
            $resolver = $this->getJobResolver($input->getArgument('job'), $input->getParameterOption('--force') !== false, $input->getParameterOption('--schedule_now') !== false);
        } else {
            $resolver = $this->getContainer()->get('cron.resolver');
        }

        if($input->getParameterOption('--script-name') !== false) {
            $resolver->setScriptName((string)$input->getParameterOption('--script-name'));
        }

        $cron->setResolver($resolver);

        $time = microtime(true);
        $dbReport = $cron->run();

        while ($cron->isRunning()) {
            time_nanosleep(0, 100000000);
        }

        $output->writeln('time: ' . (microtime(true) - $time));

        $manager = $this->getContainer()->get('cron.manager');
        $manager->saveReports($dbReport->getReports());

        return 0;
    }

    /**
     * @param  string                    $jobName
     * @param  bool                      $force
     * @return ArrayResolver
     * @throws \InvalidArgumentException
     */
    protected function getJobResolver($jobName, $force = false, $schedule_now = false)
    {
        $dbJob = $this->queryJob($jobName);

        if (!$dbJob) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        if (!$dbJob->getEnabled() && !$force) {
            throw new \InvalidArgumentException('Job is disabled, run with --force to force schedule it.');
        }

        $finder = new PhpExecutableFinder();
        $phpExecutable = $finder->find();
        $rootDir = $this->getContainer()->getParameter('kernel.project_dir');
        $pattern = !$schedule_now ? $dbJob->getSchedule() : '* * * * *';

        $resolver = new ArrayResolver();

        $job = new ShellJob();
        $job->setCommand(escapeshellarg($phpExecutable) . ' --define max_execution_time='.ini_get('max_execution_time').' --define memory_limit='.ini_get('memory_limit').' ' . $rootDir . '/bin/console ' . $dbJob->getCommand());
        $job->setSchedule(new CrontabSchedule($pattern));
        $job->raw = $dbJob;

        $resolver->addJob($job);

        return $resolver;
    }

    /**
     * @param  string  $jobName
     * @return CronJob
     */
    protected function queryJob($jobName)
    {
        $job = $this->getContainer()->get('cron.manager')
            ->getJobByName($jobName);

        return $job;
    }
}
