<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cron\CronBundle\Cron\Manager;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class ManagerTest extends \PHPUnit\Framework\TestCase
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

    public function getJobByName()
    {
        $manager = $this->getManagerWithRepo('findOneBy');
        $jobName = 'testJobName';
        $findByArguments = array(array('name' => $jobName), null, null, null);

        $this->assertEquals($findByArguments, $manager->getJobByName($jobName));
    }

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

        $job = new \Cron\Job\ShellJob();
        $job->setCommand('ls');
        $job->raw = '';

        $report = $this->getMockBuilder('Cron\Report\JobReport')
            ->setConstructorArgs(array($job))
            ->getMock();

        $report->expects($this->any())
            ->method('getJob')
            ->will($this->returnValue($job));
        $report->expects($this->exactly(2))
            ->method('getStartTime');
        $report->expects($this->once())
            ->method('getEndTime');

        $manager->saveReports(array($report));
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

        $manager->deleteJob(new \Cron\CronBundle\Entity\CronJob());
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

        $manager->saveJob(new \Cron\CronBundle\Entity\CronJob());
    }

    protected function getManagerWithRepo($repoCall)
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

    protected function buildRepo()
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function buildEm()
    {
        return $this->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function buildRegistry()
    {
        return $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getManager($registry)
    {
        $manager = new Manager($registry);

        return $manager;
    }
}
