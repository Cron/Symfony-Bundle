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

use Cron\CronBundle\Entity\CronJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Cron\CronBundle\Cron\ManagerDecorator;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronListCommand extends ContainerAwareCommand
{
    /**
     * @var ManagerDecorator|null
     */
    private $cronManager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:list')
            ->setDescription('List all available crons')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cronManager = Helper\CronCommandHelper::setManagerHelper($this->getApplication(), $input);

        $jobs = $this->queryJobs($input);

        foreach ($jobs as $job) {
            $state = $job->getEnabled() ? 'x' : ' ';
            $output->writeln(sprintf(' [%s] %s', $state, $job->getName()));
        }
    }

    /**
     * @return CronJob[]
     */
    protected function queryJobs($input)
    {
        return $this->cronManager->listJobs();
    }
}