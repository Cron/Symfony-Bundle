<?php
/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cron\CronBundle\Command;

use Cron\CronBundle\Cron\CronCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Alexander Lokhman <alex.lokhman@gmail.com>
 */
class CronStopCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:stop')
            ->setDescription('Stops cron scheduler');
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pidFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.CronStartCommand::PID_FILE;
        if (!file_exists($pidFile)) {
            return 0;
        }

        if (!extension_loaded('pcntl')) {
            throw new \RuntimeException('This command needs the pcntl extension to run.');
        }

        if (!posix_kill(file_get_contents($pidFile), SIGINT)) {
            if (!unlink($pidFile)) {
                throw new \RuntimeException('Unable to stop scheduler.');
            }

            $output->writeln(sprintf('<comment>%s</comment>', 'Unable to kill cron scheduler process. Scheduler will be stopped before the next run.'));

            return 0;
        }

        unlink($pidFile);

        $output->writeln(sprintf('<info>%s</info>', 'Cron scheduler is stopped.'));

        return 0;
    }
}
