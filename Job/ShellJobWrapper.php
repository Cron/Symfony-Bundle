<?php declare(strict_types=1);

namespace Cron\CronBundle\Job;

use Cron\CronBundle\Entity\CronJob;
use Cron\Job\ShellJob;
use Symfony\Component\Process\Process;

class ShellJobWrapper extends ShellJob
{
    public ?CronJob $raw = null;

    public function setCommand($command)
    {
        $commandArray = explode(' ', $command);
        $this->process = new Process($commandArray);
    }
}
