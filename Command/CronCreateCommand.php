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
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */ 
class CronCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:create')
            ->setDescription('Create a cron job');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new CronJob();

        $output->writeln('');
        $output->writeln('<question>Name</question>');
        $output->writeln('<info>The unique name how the job will be referenced.</info>');
        $job->setName($this->getDialogHelper()->askAndValidate(
            $output,
            ': ',
            function($input) { return $this->validateJobName($input); },
            false
        ));

        $output->writeln('');
        $output->writeln('<question>Command</question>');
        $output->writeln('<info>The command to execute. You may add extra arguments.</info>');
        $job->setCommand($this->getDialogHelper()->askAndValidate(
            $output,
            ': ',
            function($input) { return $this->validateCommand($input); },
            false
        ));

        $output->writeln('');
        $output->writeln('<question>Schedule</question>');
        $output->writeln('<info>The schedule in the crontab syntax.</info>');
        $job->setSchedule($this->getDialogHelper()->askAndValidate(
            $output,
            ': ',
            function($input) { return $this->validateSchedule($input); },
            false
        ));

        $output->writeln('');
        $output->writeln('<question>Description</question>');
        $output->writeln('<info>Some more information about the job.</info>');
        $job->setDescription($this->getDialogHelper()->askAndValidate(
            $output,
            '<question>Description</question>: ',
            function($input) { return (string) $input; }
        ));

        $output->writeln('');
        $output->writeln('<question>Enable</question>');
        $output->writeln('<info>Should the cron be enabled.</info>');
        $job->setEnabled($this->getDialogHelper()->askConfirmation(
                $output,
                '[Y/n]: ',
                true
            ));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($job);
        $em->flush();

        $output->writeln('');
        $output->writeln(sprintf('<info>Cron "%s" was created..</info>', $job->getName()));
    }

    /**
     * Validate the job name.
     *
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function validateJobName($name)
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
     * @param string $command
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function validateCommand($command)
    {
        $parts = explode(' ', $command);
        $this->getApplication()->get((string)$parts[0]);

        return $command;
    }

    /**
     * Validate the schedule.
     *
     * @param string $schedule
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function validateSchedule($schedule)
    {
        //TODO Validate the schedule.

        return $schedule;
    }

    /**
     * @param string $jobName
     * @return CronJob
     */
    protected function queryJob($jobName)
    {
        return $this->getContainer()->get('doctrine')->getRepository('CronCronBundle:CronJob')
            ->findOneBy(array(
                    'name' => $jobName,
                ));
    }

    /**
     * @return DialogHelper
     */
    private function getDialogHelper()
    {
        return $this->getHelperSet()->get('dialog');
    }
}
