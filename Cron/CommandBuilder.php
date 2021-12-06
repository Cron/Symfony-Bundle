<?php
/**
 * This file is part of the CronSymfonyBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cron\CronBundle\Cron;

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CommandBuilder
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $phpExecutable;

    /**
     * @param string $environment
     */
    public function __construct($environment)
    {
        $this->environment = $environment;

        $finder = new PhpExecutableFinder();
        $this->phpExecutable = $finder->find();
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function build($command, $scriptName = null)
    {
        return sprintf('%s %s %s %s --env=%s', $this->phpExecutable, ' --define max_execution_time='.ini_get('max_execution_time').' --define memory_limit='.ini_get('memory_limit'), $scriptName ?? $_SERVER['SCRIPT_NAME'], $command, $this->environment);
    }
}
