<?php
declare(strict_types=1);

namespace Cron\CronBundle\Job;

use Cron\CronBundle\Entity\CronJob;
use Cron\Job\ShellJob;

class ShellJobWrapper extends ShellJob
{
    /**
     * @var CronJob|null
     */
    public ?CronJob $raw = null;
}
