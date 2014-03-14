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
class CronRunCommandTest extends WebTestCase
{
    public function testNoJobs()
    {
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $resolver = $this->getMock('Cron\CronBundle\Cron\Resolver');
        $resolver
            ->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array()));

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertContains('time:', $commandTester->getDisplay());
    }

    public function testOneJob()
    {
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');
        $manager
            ->expects($this->once())
            ->method('saveReports')
            ->with($this->isType('array'));

        $job = new \Cron\Job\ShellJob();

        $resolver = $this->getMock('Cron\CronBundle\Cron\Resolver');
        $resolver
            ->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue(array(
                        $job
                    )));

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $this->assertContains('time:', $commandTester->getDisplay());
    }

    public function testNamedJob()
    {
        $this->setExpectedException('InvalidArgumentException');
        $manager = $this->getMock('Cron\CronBundle\Cron\Manager');
        $resolver = $this->getMock('Cron\CronBundle\Cron\Resolver');

        $command = $this->getCommand($manager, $resolver);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
                'job' => 'jobName',
            ));

        $this->assertContains('time:', $commandTester->getDisplay());
    }

    protected function getCommand($manager, $resolver)
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);
        $kernel->getContainer()->set('cron.resolver', $resolver);

        $application = new Application($kernel);
        $application->add(new \Cron\CronBundle\Command\CronRunCommand());

        return $application->find('cron:run');
    }
}
