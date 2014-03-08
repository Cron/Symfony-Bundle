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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */ 
class CronDisableCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:disable')
            ->setDescription('Disable a cron job')
            ->addArgument('job', InputArgument::REQUIRED, 'The job to disable');
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

        $job->setEnabled(false);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($job);
        $em->flush();

        $output->writeln(sprintf('Cron "%s" disabled', $job->getName()));
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
}
