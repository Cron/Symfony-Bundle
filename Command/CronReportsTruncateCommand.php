<?php declare(strict_types=1);
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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Wouter van der Loop <woutervdl@toppy.nl>
 */
class CronReportsTruncateCommand extends CronCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cron:reports:truncate')
            ->setDescription('Trucate reports after given days')
            ->addArgument(
                'days',
                InputArgument::OPTIONAL,
                'Days to clear after',
                3
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = (int)$input->getArgument('days');

        $output->writeln(
            sprintf(
                '<info>Clearing cron reports older than %s days</info>',
                $days
            )
        );

        $this->getContainer()->get('cron.manager')->truncateReports($days);

        return 0;
    }
}
