<?php declare(strict_types=1);

namespace Cron\CronBundle\Job;

use Cron\CronBundle\Entity\CronJob;
use Cron\Job\ShellJob;
use Symfony\Component\Process\Process;

class ShellJobWrapper extends ShellJob
{
    public ?CronJob $raw = null;

    public function setCommand($command, $cwd = null, ?array $env = null, $input = null, $timeout = 60, array $options = [])
    {
        $commandArray = explode(' ', $command);

        $this->process = new Process($commandArray);
    }
}
