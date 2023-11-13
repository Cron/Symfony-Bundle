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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CronCreateCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cron:create')
            ->setDescription('Create a cron job')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The job name')
            ->addOption('command', null, InputOption::VALUE_REQUIRED, 'The job command')
            ->addOption('schedule', null, InputOption::VALUE_REQUIRED, 'The job schedule')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'The job description')
            ->addOption('enabled', null, InputOption::VALUE_REQUIRED, 'Is the job enabled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = new CronJob();

        $name = $input->getOption('name');
        if (is_null($name)) {
            $output->writeln('');
            $output->writeln('<info>The unique name how the job will be referenced.</info>');
            $question = new Question('<question>Name:</question> ', false);
            $name = $this->getQuestionHelper()->ask($input, $output, $question);
        }
        $this->validateJobName($name);
        $job->setName($name);

        $command = $input->getOption('command');
        if (is_null($command)) {
            $output->writeln('');
            $output->writeln('<info>The command to execute. You may add extra arguments.</info>');
            $question = new Question('<question>Command:</question> ', false);
            $command = $this->getQuestionHelper()->ask($input, $output, $question);
        }
        $this->validateCommand($command);
        $job->setCommand($command);

        $schedule = $input->getOption('schedule');
        if (is_null($schedule)) {
            $output->writeln('');
            $output->writeln('<info>The schedule in the crontab syntax.</info>');
            $question = new Question('<question>Schedule:</question> ', false);
            $schedule = $this->getQuestionHelper()->ask($input, $output, $question);
        }
        $this->validateSchedule($schedule);
        $job->setSchedule($schedule);

        $description = $input->getOption('description');
        if (is_null($description)) {
            $output->writeln('');
            $output->writeln('<info>Some more information about the job.</info>');
            $question = new Question('<question>Description:</question> ', false);
            $description = $this->getQuestionHelper()->ask($input, $output, $question);
        }
        $job->setDescription($description);

        $enabled = $input->getOption('enabled');
        if (is_null($enabled)) {
            $output->writeln('');
            $output->writeln('<info>Should the cron be enabled.</info>');
            $question = new ConfirmationQuestion('<question>Enable?</question> [y/n]: ', false, '/^(y)/i');
            $enabled = $this->getQuestionHelper()->ask($input, $output, $question);
        }
        $job->setEnabled(boolval($enabled));

        $this->getContainer()->get('cron.manager')
            ->saveJob($job);

        $output->writeln('');
        $output->writeln(sprintf('<info>Cron "%s" was created..</info>', $job->getName()));

        return 0;
    }

    /**
     * Validate the job name.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateJobName($name): string
    {
        if (!$name || strlen($name) == 0) {
            throw new \InvalidArgumentException('Please set a name.');
        }

        if ($this->queryJob($name)) {
            throw new \InvalidArgumentException('Name already in use.');
        }

        return $name;
    }

    /**
     * Validate the command.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateCommand(string $command): string
    {
        $parts = explode(' ', $command);
        $this->getApplication()->get((string) $parts[0]);

        return $command;
    }

    /**
     * Validate the schedule.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateSchedule(string $schedule): string
    {
        $this->getContainer()->get('cron.validator')
            ->validate($schedule);

        return $schedule;
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
