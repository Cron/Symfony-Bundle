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

        $job->setName($this->getDialogHelper()->askAndValidate(
            $output,
            '<question>Name</question>: ',
            function($input) { return $this->validateJobName($input); },
            false
        ));
        $job->setCommand($this->getDialogHelper()->askAndValidate(
            $output,
            '<question>Command</question>: ',
            function($input) { return $this->validateCommand($input); },
            false
        ));
        $job->setSchedule($this->getDialogHelper()->askAndValidate(
            $output,
            '<question>Schedule</question>: ',
            function($input) { return $this->validateSchedule($input); },
            false
        ));
        $job->setDescription($this->getDialogHelper()->askAndValidate(
            $output,
            '<question>Description</question>: ',
            function($input) { return (string) $input; }
        ));
        $job->setEnabled($this->getDialogHelper()->askConfirmation(
                $output,
                '<question>Enable the job</question> [Y/n]: ',
                true
            ));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($job);
        $em->flush();

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
        $this->getApplication()->get($command);

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
