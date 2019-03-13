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
use Cron\CronBundle\Entity\CronJobRepository;
use Cron\CronBundle\Entity\CronReport;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class Manager
{
    /**
     * @var RegistryInterface
     */
    protected $manager;

    /**
     * @param RegistryInterface $registry
     */
    function __construct(RegistryInterface $registry)
    {
        $this->manager = $registry->getManagerForClass(CronJob::class);
    }

    /**
     * @return CronJobRepository
     */
    protected function getJobRepo()
    {
        return $this->manager->getRepository(CronJob::class);
    }

    /**
     * @param CronReport[] $reports
     */
    public function saveReports(array $reports)
    {
        foreach ($reports as $report) {
            $dbReport = new CronReport();
            $dbReport->setJob($report->getJob()->raw);
            $dbReport->setOutput(implode("\n", (array) $report->getOutput()));
            $dbReport->setExitCode($report->getJob()->getProcess()->getExitCode());
            $dbReport->setRunAt(\DateTime::createFromFormat('U.u', (string) $report->getStartTime()));
            $dbReport->setRunTime($report->getEndTime() - $report->getStartTime());
            $this->manager->persist($dbReport);
        }
        $this->manager->flush();
    }

    /**
     * @return CronJob[]
     */
    public function listJobs()
    {
        return $this->getJobRepo()
            ->findBy(array(), array(
                    'name' => 'asc',
                ));
    }

    /**
     * @return CronJob[]
     */
    public function listEnabledJobs()
    {
        return $this->getJobRepo()
            ->findBy(array(
                    'enabled' => 1,
                ), array(
                    'name' => 'asc',
                ));
    }

    /**
     * @param CronJob $job
     */
    public function saveJob(CronJob $job)
    {
        $this->manager->persist($job);
        $this->manager->flush();
    }

    /**
     * @param  string  $name
     * @return CronJob
     */
    public function getJobByName($name)
    {
        return $this->getJobRepo()
            ->findOneBy(array(
                    'name' => $name,
                ));
    }

    /**
     * @param CronJob $job
     */
    public function deleteJob(CronJob $job)
    {
        $this->manager->remove($job);
        $this->manager->flush();
    }
}
