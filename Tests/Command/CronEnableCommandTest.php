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
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');
        $manager
            ->expects($this->once())
            ->method('getJobByName');

        $command = $this->getCommand($manager);

        $this->setExpectedException('InvalidArgumentException');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'job'     => 'jobName',
        ));
    }

    public function testEnable()
    {
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');

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
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');
        $command = $this->getCommand($manager);

        $this->setExpectedException('RuntimeException');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    protected function getCommand($manager)
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);

        $application = new Application($kernel);
        $application->add(new CronEnableCommand());

        return $application->find('cron:enable');
    }
}
