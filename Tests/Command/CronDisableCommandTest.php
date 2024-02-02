<?php declare(strict_types=1);
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cron\CronBundle\Tests\Command;

use Cron\CronBundle\Command\CronDisableCommand;
use Cron\CronBundle\Cron\Manager;
use Cron\CronBundle\Entity\CronJob;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronDisableCommandTest extends WebTestCase
{
    public function testUnknownJob(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('getJobByName');

        $command = $this->getCommand($manager);

        $this->expectException(InvalidArgumentException::class);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'job'     => 'jobName',
        ));
    }

    public function testDisable(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $job = new CronJob();
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
        $this->assertFalse($job->getEnabled());
    }

    public function testNoJobArgument(): void
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $command = $this->getCommand($manager);

        $this->expectException(RuntimeException::class);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    protected function getCommand(Manager $manager): Command
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $kernel->getContainer()->set('cron.manager', $manager);

        $application = new Application($kernel);
        $application->add(new CronDisableCommand($kernel->getContainer()));

        return $application->find('cron:disable');
    }
}
