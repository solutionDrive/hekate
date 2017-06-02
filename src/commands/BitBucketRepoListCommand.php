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

use Bitbucket\API\Http\Response\Pager;
use Bitbucket\API\Repositories;
use Buzz\Message\Response;
use sd\hekate\lib\BitbucketRepositoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addOption('username', 'u', InputArgument::OPTIONAL, 'The username of the bitbucket-User')
            ->addOption('password', 'p', InputArgument::OPTIONAL, 'The password of the bitbucket-User')
            ->addOption('account', 'a', InputArgument::OPTIONAL, 'account from which private repositories will be fetched')
        ;

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $account = $input->getOption('account');

        $repositoryList = new BitbucketRepositoryList(new Repositories());
        $repositoryList->setCredentials($username, $password);

        /** @var Pager $pager */
        $pager = $repositoryList->getPager($account);
        $repoInfo = [];

        do {
            $responseForPage = $pager->getCurrent();
            $reposOfPage = json_decode($responseForPage->getContent());
            foreach ($reposOfPage->values as $repo) {
                $repoInfo[$repo->name]['name'] = $repo->name;
            }
            $pager->fetchNext();
        } while ($pager->hasNext());

        $table = new Table($output);
        $table->setHeaders(['name']);
        $table->setRows($repoInfo);
        $table->render();
    }
}
