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
use Cron\CronBundle\Cron\Resolver;
use Cron\Executor\Executor;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */ 
class CronRunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cron:run')
            ->setDescription('Runs any currently schedule cron jobs')
            ->addArgument('job', InputArgument::OPTIONAL, 'Run only this job (if enabled)')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force the current job.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cron = new Cron();
        $cron->setExecutor($this->getContainer()->get('cron.executor'));
        if ($input->getArgument('job')) {
            $resolver = $this->getJobResolver($input->getArgument('job'), $input->hasOption('force'));
        } else {
            $resolver = $this->getContainer()->get('cron.resolver');
        }
        $cron->setResolver($resolver);

        $time = microtime(true);
        $report = $cron->run();

        while($cron->isRunning()) {}

        $output->writeln('time: ' . (microtime(true) - $time));

        foreach ($report->getReports() as $report) {
            var_dump($report->getOutput());
        }
    }

    protected function getJobResolver($jobName, $force = false)
    {
        $dbJob = $this->getContainer()->get('doctrine')->getRepository('CronCronBundle:CronJob')
            ->findOneBy(array(
                    'enabled' => 1,
                    'name' => $jobName,
                ));

        if (!$dbJob) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        $finder = new PhpExecutableFinder();
        $phpExecutable = $finder->find();
        $rootDir = dirname($this->getContainer()->getParameter('kernel.root_dir'));

        $job = new ShellJob();
        $job->setCommand($phpExecutable . ' app/console ' . $dbJob->getCommand(), $rootDir);
        $job->setSchedule(new CrontabSchedule($dbJob->getSchedule()));

        $resolver = new ArrayResolver();
        $resolver->addJob($job);

        return $resolver;
    }
}
