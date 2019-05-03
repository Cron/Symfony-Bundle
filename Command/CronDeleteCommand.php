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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Cron\CronBundle\Cron\ManagerDecorator;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronDeleteCommand extends ContainerAwareCommand
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
        $this->setName('cron:delete')
            ->setDescription('Delete a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to delete')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cronManager = Helper\CronCommandHelper::setManagerHelper($this->getApplication(), $input);
        
        $job = $this->queryJob($input->getArgument('job'));

        if (!$job) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        if ($job->getEnabled()) {
            throw new \InvalidArgumentException('The job should be disabled first.');
        }

        $output->writeln(sprintf('<info>You are about to delete "%s".</info>', $job->getName()));

        $question = new ConfirmationQuestion('<question>Delete this job</question> [N/y]: ', false, '/^(y)/i');

        if (!$this->getQuestionHelper()->ask($input, $output, $question)) {
            return;
        }

        $this->cronManager
            ->deleteJob($job);

        $output->writeln(sprintf('<info>Cron "%s" was deleted.</info>', $job->getName()));
    }

    /**
     * @param  string  $jobName
     * @return CronJob
     */
    protected function queryJob($jobName)
    {
        return $this->cronManager
            ->getJobByName($jobName);
    }

    /**
     * @return QuestionHelper
     */
    private function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }
}
