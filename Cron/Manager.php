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
use Cron\CronBundle\Entity\CronReportRepository;
use Cron\CronBundle\Job\ShellJobWrapper;
use Cron\Report\JobReport;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class Manager
{
    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
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
     * @return CronReportRepository
     */
    protected function getReportRepo()
    {
        return $this->manager->getRepository(CronReport::class);
    }

    /**
     * @param JobReport[] $reports
     */
    public function saveReports(array $reports): void
    {
        $connection = $this->manager->getConnection();
        if($connection instanceof Connection && true === method_exists($connection, 'ping') && false === $connection->ping()){
            $connection->close();
            $connection->connect();
        }
        foreach ($reports as $report) {
            $job = $report->getJob();
            if (! $job instanceof ShellJobWrapper) {
                continue;
            }
            $dbReport = new CronReport();
            $dbReport->setJob($job->raw);
            $dbReport->setOutput(implode("\n", (array) $report->getOutput()));
            $dbReport->setError(implode("\n", (array) $report->getError()));
            $dbReport->setExitCode($report->getJob()->getProcess()->getExitCode());
            $dbReport->setRunAt(\DateTime::createFromFormat('U.u', number_format($report->getStartTime(), 6, '.', '')));
            $dbReport->getRunAt()->setTimezone(new \DateTimeZone(getenv('TZ') ?: date_default_timezone_get()));
            $dbReport->setRunTime($report->getEndTime() - $report->getStartTime());
            $this->manager->persist($dbReport);
        }
        $this->manager->flush();
    }

    public function truncateReports(int $days = 3): void
    {
        $connection = $this->manager->getConnection();
        if($connection instanceof Connection && true === method_exists($connection, 'ping') && false === $connection->ping()){
            $connection->close();
            $connection->connect();
        }
        $queryBuilder = $this->getReportRepo()->createQueryBuilder('cr');

        $dateToClear = (new \DateTime('today'))
            ->modify("-{$days} days")
            ->format('Y-m-d H:i:s');

        $queryBuilder
            ->delete(
                CronReport::class,
                'cr'
            )
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lte(
                        'cr.runAt',
                        $queryBuilder->expr()->literal($dateToClear)
                    )
                )
            )
            ->getQuery()
            ->execute();
    }

    /**
     * @return CronJob[]
     */
    public function listJobs(): array
    {
        return $this->getJobRepo()
            ->findBy(array(), array(
                    'name' => 'asc',
                ));
    }

    /**
     * @return CronJob[]
     */
    public function listEnabledJobs(): array
    {
        return $this->getJobRepo()
            ->findBy(array(
                    'enabled' => 1,
                ), array(
                    'name' => 'asc',
                ));
    }

    public function saveJob(CronJob $job): void
    {
        $this->manager->persist($job);
        $this->manager->flush();
    }

    public function getJobByName(string $name): CronJob
    {
        return $this->getJobRepo()
            ->findOneBy(array(
                    'name' => $name,
                ));
    }

    public function deleteJob(CronJob $job): void
    {
        $this->manager->remove($job);
        $this->manager->flush();
    }
}
