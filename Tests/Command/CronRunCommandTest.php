<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $resolver = $this->getMockBuilder('Cron\CronBundle\Cron\Resolver')
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
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $job = new \Cron\Job\ShellJob();

        $resolver = $this->getMockBuilder('Cron\CronBundle\Cron\Resolver')
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
        $manager = $this->getMockBuilder('Cron\CronBundle\Cron\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $resolver = $this->getMockBuilder('Cron\CronBundle\Cron\Resolver')
            ->disableOriginalConstructor()
            ->getMock();

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
                'job' => 'jobName',
            ));

        $this->assertStringContainsString('time:', $commandTester->getDisplay());
    }

    protected function getCommand($manager, $resolver)
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);
        $kernel->getContainer()->set('cron.resolver', $resolver);

        $application = new Application($kernel);
        $application->add(new \Cron\CronBundle\Command\CronRunCommand($kernel->getContainer()));

        return $application->find('cron:run');
    }
}
