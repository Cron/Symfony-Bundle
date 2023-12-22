<?php declare(strict_types=1);
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cron\CronBundle\Tests\Cron;

use Cron\CronBundle\Cron\Manager;
use Cron\CronBundle\Entity\CronJob;
use Cron\CronBundle\Job\ShellJobWrapper;
use Cron\Report\JobReport;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class ManagerTest extends TestCase
{
    public function testListJobs()
    {
        $manager = $this->getManagerWithRepo('findBy');
        $findByArguments = array(array(), array('name' => 'asc'), null, null);

        $this->assertEquals($findByArguments, $manager->listJobs());
    }

    public function testListEnabledJobs()
    {
        $manager = $this->getManagerWithRepo('findBy');
        $findByArguments = array(array('enabled' => 1), array('name' => 'asc'), null, null);

        $this->assertEquals($findByArguments, $manager->listEnabledJobs());
    }

    public function getJobByName(): void
    {
        $manager = $this->getManagerWithRepo('findOneBy');
        $jobName = 'testJobName';
        $findByArguments = array(array('name' => $jobName), null, null, null);

        $this->assertEquals($findByArguments, $manager->getJobByName($jobName));
    }

    /**
     * @throws Exception
     */
    public function testSaveReportsEmpty()
    {
        $entityManager = $this->buildEm();
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $registry = $this->buildRegistry();
        $registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        $manager = $this->getManager($registry);

        $manager->saveReports(array());
    }

    /**
     * @throws Exception
     */
    public function testSaveReports()
    {
        $entityManager = $this->buildEm();
        $entityManager
            ->expects($this->once())
            ->method('flush');
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $registry = $this->buildRegistry();
        $registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        $manager = $this->getManager($registry);

        $job = new ShellJobWrapper();
        $job->setCommand('ls');

        $report = $this->getMockBuilder(JobReport::class)
            ->setConstructorArgs(array($job))
            ->getMock();

        $report->expects($this->any())
            ->method('getJob')
            ->will($this->returnValue($job));
        $report->expects($this->exactly(2))
            ->method('getStartTime');
        $report->expects($this->once())
            ->method('getEndTime');

        $manager->saveReports([$report]);
    }

    public function testDeleteJob()
    {
        $entityManager = $this->buildEm();
        $entityManager
            ->expects($this->once())
            ->method('flush');
        $entityManager
            ->expects($this->once())
            ->method('remove');

        $registry = $this->buildRegistry();
        $registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        $manager = $this->getManager($registry);

        $manager->deleteJob(new CronJob());
    }

    public function testSaveJob()
    {
        $entityManager = $this->buildEm();
        $entityManager
            ->expects($this->once())
            ->method('flush');
        $entityManager
            ->expects($this->once())
            ->method('persist');

        $registry = $this->buildRegistry();
        $registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        $manager = $this->getManager($registry);

        $manager->saveJob(new CronJob());
    }

    protected function getManagerWithRepo($repoCall): Manager
    {
        $jobRepo = $this->buildRepo();
        $jobRepo
            ->expects($this->once())
            ->method($repoCall)
            ->will($this->returnCallback(function() {
                        return func_get_args();
                    }));

        $registry = $this->buildRegistry();
        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($jobRepo));

        $entityManager = $this->buildEm();
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($jobRepo));
        $registry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->will($this->returnValue($entityManager));

        return $this->getManager($registry);
    }

    protected function buildRepo(): MockObject
    {
        return $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function buildEm(): MockObject
    {
        return $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function buildRegistry(): MockObject
    {
        return $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getManager($registry): Manager
    {
        return new Manager($registry);
    }
}
