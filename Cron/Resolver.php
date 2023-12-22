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

use Cron\CronBundle\Entity\CronJob;
use Cron\CronBundle\Job\ShellJobWrapper;
use Cron\Job\JobInterface;
use Cron\Job\ShellJob;
use Cron\Resolver\ResolverInterface;
use Cron\Schedule\CrontabSchedule;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class Resolver implements ResolverInterface
{
    private ?string $scriptName = null;

    public function __construct(
        private readonly Manager        $manager,
        private readonly CommandBuilder $commandBuilder,
        private readonly string $rootDir
    )
    {
    }

    /**
     * Return all available jobs.
     *
     * @return JobInterface[]
     */
    public function resolve(): array
    {
        $jobs = $this->manager->listEnabledJobs();

        return array_map(array($this, 'createJob'), $jobs);
    }

    /**
     * Overrides the script name used by the command builder to build the command.
     */
    public function setScriptName(string $scriptName): void
    {
        $this->scriptName = $scriptName;
    }

    /**
     * Transform a CronJon into a ShellJob.
     */
    protected function createJob(CronJob $dbJob): ShellJob
    {
        $job = new ShellJobWrapper();
        $job->setCommand($this->commandBuilder->build($dbJob->getCommand(), $this->scriptName), $this->rootDir);
        $job->setSchedule(new CrontabSchedule($dbJob->getSchedule()));
        $job->raw = $dbJob;

        return $job;
    }
}
