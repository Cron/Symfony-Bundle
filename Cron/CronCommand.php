<?php


namespace Cron\CronBundle\Cron;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CronCommand extends Command
{
    /** ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    public function getContainer()
    {
        return $this->container;
    }
}
