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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */ 
class CronDeleteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:delete')
            ->setDescription('Delete a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to delete');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $this->queryJob($input->getArgument('job'));

        if (!$job) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        if ($job->getEnabled()) {
            throw new \InvalidArgumentException('The job should be disabled first.');
        }

        $output->writeln(sprintf('<info>You are about to delete "%s".</info>', $job->getName()));
        if (!$this->getDialogHelper()->askConfirmation(
            $output,
            '<question>Delete this job</question> [N/y]: ',
            false
        )) {
            return;
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->remove($job);
        $em->flush();

        $output->writeln(sprintf('<info>Cron "%s" was deleted.</info>', $job->getName()));
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
