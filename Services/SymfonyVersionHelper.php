<?php

namespace Cron\CronBundle\Services;

use AppKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;

class SymfonyVersionHelper
{
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getRootDir()
    {
        if(Kernel::MAJOR_VERSION > 4)
        {
	        return $this->container->getParameter('kernel.root_dir');
        }else{
	        return $this->container->getParameter('kernel.root_dir');
        }
    }
}