<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cron\CronBundle\Command\CronEnableCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronEnableCommandTest extends WebTestCase
{
    public function testUnknownJob()
    {
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('getJobByName');

        $command = $this->getCommand($manager);

        $this->expectException('InvalidArgumentException');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'job'     => 'jobName',
        ));
    }

    public function testEnable()
    {
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $job = new \Cron\CronBundle\Entity\CronJob();
        $manager
            ->expects($this->once())
            ->method('getJobByName')
            ->will($this->returnValue($job));

        $command = $this->getCommand($manager);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'job'     => 'jobName',
        ));

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertEquals(true, $job->getEnabled());
    }

    public function testNoJobArgument()
    {
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $command = $this->getCommand($manager);

        $this->expectException('RuntimeException');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    protected function getCommand($manager)
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);

        $application = new Application($kernel);
        $application->add(new CronEnableCommand($kernel->getContainer()));

        return $application->find('cron:enable');
    }
}
