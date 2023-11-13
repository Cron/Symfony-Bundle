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
use Cron\CronBundle\Entity\CronJob;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronDeleteCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cron:delete')
            ->setDescription('Delete a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = $this->queryJob($input->getArgument('job'));

        if (!$job) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        if ($job->getEnabled()) {
            throw new \InvalidArgumentException('The job should be disabled first.');
        }

        $output->writeln(sprintf('<info>You are about to delete "%s".</info>', $job->getName()));

        // Defaults to NO if input is interactive
        // Defaults to YES otherwise
        $question = new ConfirmationQuestion(
            '<question>Delete this job</question> [N/y]: ',
            $input->isInteractive(),
            '/^(y)/i'
        );

        if (!$this->getQuestionHelper()->ask($input, $output, $question)) {
            return 0;
        }

        $this->getContainer()->get('cron.manager')
            ->deleteJob($job);

        $output->writeln(sprintf('<info>Cron "%s" was deleted.</info>', $job->getName()));

        return 0;
    }

    protected function queryJob(string $jobName): CronJob
    {
        return $this->getContainer()->get('cron.manager')
            ->getJobByName($jobName);
    }

    private function getQuestionHelper(): HelperInterface
    {
        return $this->getHelperSet()->get('question');
    }
}
