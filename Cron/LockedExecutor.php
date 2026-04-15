<?php declare(strict_types=1);

namespace Cron\CronBundle\Cron;

use Cron\CronBundle\Job\ShellJobWrapper;
use Cron\Executor\Executor;
use Cron\Job\JobInterface;
use Cron\Report\CronReport;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

/**
 * Executor that locks jobs to prevent concurrent execution of the same job.
 */
class LockedExecutor extends Executor
{
    private ?LockFactory $lockFactory = null;
    private array $locks = [];

    /**
     * Set the lock factory
     */
    public function setLockFactory(LockFactory $lockFactory): void
    {
        $this->lockFactory = $lockFactory;
    }

    /**
     * Override the startProcesses method to add locking
     */
    protected function startProcesses(CronReport $report): void
    {
        if (!$this->lockFactory) {
            // Fall back to parent implementation if no lock factory is set
            parent::startProcesses($report);
            return;
        }

        foreach ($this->sets as $set) {
            $job = $set->getJob();

            // Skip jobs that are already running
            if ($this->isJobLocked($job)) {
                continue;
            }

            // Try to acquire a lock for this job
            $lock = $this->acquireLock($job);
            if (!$lock) {
                // Skip if we couldn't get a lock
                continue;
            }

            // Store the lock with the job
            $this->locks[spl_object_hash($job)] = $lock;

            // Add report and run the job
            $report->addJobReport($set->getReport());
            $set->run();
        }
    }

    /**
     * Check if a job is already locked/running
     */
    private function isJobLocked(JobInterface $job): bool
    {
        if (!$job instanceof ShellJobWrapper || !$job->raw) {
            // We can't determine the job ID, so assume it's not locked
            return false;
        }

        // Create a lock without acquiring it to check if someone else has it
        $lockName = $this->getLockName($job);
        $lock = $this->lockFactory->createLock($lockName);
        
        return !$lock->acquire(false);
    }

    /**
     * Try to acquire a lock for a job
     */
    private function acquireLock(JobInterface $job): ?LockInterface
    {
        if (!$job instanceof ShellJobWrapper || !$job->raw) {
            // No need to lock if we can't identify the job
            return null;
        }

        $lockName = $this->getLockName($job);
        $lock = $this->lockFactory->createLock($lockName, 3600); // 1 hour TTL to prevent stale locks
        
        if ($lock->acquire(false)) {
            return $lock;
        }
        
        return null;
    }

    /**
     * Get a unique lock name for the job
     */
    private function getLockName(JobInterface $job): string
    {
        if ($job instanceof ShellJobWrapper && $job->raw) {
            return 'cron_job_' . $job->raw->getId();
        }
        
        // Fallback if no job ID is available
        return 'cron_job_' . md5(serialize($job));
    }

    /**
     * Override isRunning to release locks when jobs finish
     */
    public function isRunning(): bool
    {
        $running = false;
        
        foreach ($this->sets as $set) {
            $job = $set->getJob();
            $jobHash = spl_object_hash($job);
            
            if ($job->isRunning()) {
                $running = true;
            } elseif (isset($this->locks[$jobHash])) {
                // Job is done, release the lock
                $this->locks[$jobHash]->release();
                unset($this->locks[$jobHash]);
            }
        }
        
        return $running;
    }
} 