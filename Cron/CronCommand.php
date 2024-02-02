<?php declare(strict_types=1);

namespace Cron\CronBundle\Cron;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CronCommand extends Command
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
