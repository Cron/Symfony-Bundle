<?php declare(strict_types=1);

namespace Cron\CronBundle\Tests\Fixtures\App\app;

use Cron\CronBundle\CronCronBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return array(
          new FrameworkBundle(),
          new DoctrineBundle(),

          new CronCronBundle(),
        );
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_test.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/CronSymfonyBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/CronSymfonyBundle/logs';
    }
}
