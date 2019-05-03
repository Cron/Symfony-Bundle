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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Alexander Lokhman <alex.lokhman@gmail.com>
 */
class CronStartCommand extends ContainerAwareCommand
{
    const PID_FILE = '.cron-pid';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:start')
            ->setDescription('Starts cron scheduler')
            ->addOption('blocking', 'b', InputOption::VALUE_NONE, 'Run in blocking mode.')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('blocking')) {
            $output->writeln(sprintf('<info>%s</info>', 'Starting cron scheduler in blocking mode.'));
            $this->scheduler($input,$output->isVerbose() ? $output : new NullOutput(), null);

            return 0;
        }

        if (!extension_loaded('pcntl')) {
            throw new \RuntimeException('This command needs the pcntl extension to run.');
        }

        $pidFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.self::PID_FILE;

        if (-1 === $pid = pcntl_fork()) {
            throw new \RuntimeException('Unable to start the cron process.');
        } elseif (0 !== $pid) {
            if (false === file_put_contents($pidFile, $pid)) {
                throw new \RuntimeException('Unable to create process file.');
            }

            $output->writeln(sprintf('<info>%s</info>', 'Cron scheduler started in non-blocking mode...'));

            return 0;
        }

        if (-1 === posix_setsid()) {
            throw new \RuntimeException('Unable to set the child process as session leader.');
        }

        $this->scheduler($input, new NullOutput(), $pidFile);
    }

    private function scheduler(InputInterface $startInput, OutputInterface $output, $pidFile)
    {
        $command = $this->getApplication()->find('cron:run');

        $arguments = [
            'command' => 'cron:run'
        ];

        if($startInput->hasOption('connection')){
            $arguments['--connection'] = $startInput->getOption('connection');
        }

        $input = new ArrayInput($arguments);

        while (true) {
            $now = microtime(true);
            usleep((60 - ($now % 60) + (int) $now - $now) * 1e6);

            if (null !== $pidFile && !file_exists($pidFile)) {
                break;
            }
            $command->run($input, $output);
        }
    }
}
