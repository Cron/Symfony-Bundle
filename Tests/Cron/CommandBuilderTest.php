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

use Cron\CronBundle\Cron\CommandBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CommandBuilderTest extends WebTestCase
{
    public function testRenderEnvironment()
    {
        $env = rand();
        $builder = new CommandBuilder((string) $env);

        $this->assertRegExp(sprintf('/--env=%s$/', $env), $builder->build(''));
    }

    public function testEnv()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $builder = $kernel->getContainer()->get('cron.command_builder');

        $this->assertRegExp(sprintf('/ --env=%s$/', 'test'), $builder->build(''));
    }
}
