<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     14:21
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BitBucketRepoListCommand extends Command
{
    /**
     * Basic Setup
     */
    protected function configure()
    {
        $this
            ->setName('bitbucket:repo-list')
            ->setDescription('Get a List of Repositories from Bitbucket')
            ->setHelp('Command to get a List of Repositories from the ')
        ;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return parent::execute($input, $output);
    }
}
