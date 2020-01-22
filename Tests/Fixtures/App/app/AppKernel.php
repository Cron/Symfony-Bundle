<?php
namespace Cron\CronBundle\Tests\Fixtures\App\app;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
          new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
          new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

          new \Cron\CronBundle\CronCronBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_test.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir().'/CronSymfonyBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/CronSymfonyBundle/logs';
    }
}
