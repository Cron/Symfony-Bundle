<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cron\CronBundle\Command\CronRunCommand;
use Cron\CronBundle\Cron\Manager;
use Cron\CronBundle\Cron\Resolver;
use Cron\CronBundle\Job\ShellJobWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronRunCommandTest extends WebTestCase
{
    public function testNoJobs()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $resolver = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resolver
            ->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array()));

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertStringContainsString('time:', $commandTester->getDisplay());
    }

    public function testOneJob()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $job = new ShellJobWrapper();

        $resolver = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resolver
            ->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array(
                        $job
                    )));

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertStringContainsString('time:', $commandTester->getDisplay());
    }

    public function testNamedJob()
    {
        $this->expectException('InvalidArgumentException');
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resolver = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
                'job' => 'jobName',
            ));

        $this->assertStringContainsString('time:', $commandTester->getDisplay());
    }

    protected function getCommand(Manager $manager, Resolver $resolver): Command
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);
        $kernel->getContainer()->set('cron.resolver', $resolver);

        $application = new Application($kernel);
        $application->add(new CronRunCommand($kernel->getContainer()));

        return $application->find('cron:run');
    }
}
