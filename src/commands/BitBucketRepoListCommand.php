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
use sd\hekate\config\BitBucketConfiguration;
use sd\hekate\lib\BitbucketRepositoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class BitBucketRepoListCommand extends Command
{
    /** @var  BitBucketConfiguration */
    protected $bitBucketConfiguration;

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
            ->addOption('projectkey', 'k', InputArgument::OPTIONAL, 'Filter the repositories by project key')
        ;

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_initConfig();

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $projectKey = $input->getOption('projectkey');

        $username   = $this->_getUserName($input, $output, $helper);
        $password   = $this->_getPassword($input, $output, $helper);
        $account    = $this->_getAccount($input, $output, $helper);

        $repositoryList = $this->_createRepositoryList($username, $password, $account);

        $repoInfo = $this->_getInformationOfRepositories($projectKey, $repositoryList);


        $table = new Table($output);
        $table->setHeaders(['name', 'project', 'slug']);
        $table->setRows($repoInfo);
        $table->render();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $account
     * @return BitbucketRepositoryList
     */
    protected function _createRepositoryList($username, $password, $account): BitbucketRepositoryList
    {
        $repositoryList = new BitbucketRepositoryList(new Repositories());
        $repositoryList->setCredentials($username, $password);
        $repositoryList->createPager($account);
        return $repositoryList;
    }

    /**
     * @param $projectKey
     * @param $repositoryList
     * @return mixed
     */
    protected function _getInformationOfRepositories($projectKey, $repositoryList)
    {
        if (empty($projectKey) === false) {
            $repoInfo = $repositoryList->getAllForProjectKey($projectKey);
        } else {
            $repoInfo = $repositoryList->getAll();
        }
        return $repoInfo;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Helper          $helper
     * @return string
     */
    protected function _getUserName(InputInterface $input, OutputInterface $output, Helper $helper): string
    {
        /**
         * First get Input from Console
         */
        $username = $input->getOption('username');
        if (empty($username) && empty($username = $this->bitBucketConfiguration->getUserName())) {
            $question = new Question('Please enter your username: ');
            $username = $helper->ask($input, $output, $question);
        }
        return $username;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Helper          $helper
     * @return string
     */
    protected function _getPassword(InputInterface $input, OutputInterface $output, $helper): string
    {
        $password = $input->getOption('password');
        if (empty($password) && empty($password = $this->bitBucketConfiguration->getPassword())) {
            $question = new Question('Please enter your password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);
        }
        return $password;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     * @return mixed
     */
    protected function _getAccount(InputInterface $input, OutputInterface $output, $helper): mixed
    {
        $account = $input->getOption('account');
        if (empty($account) && empty($this->bitBucketConfiguration->getAccountName())) {
            $question = new Question('Please enter the name of your Bitbucket Account: ');
            $account = $helper->ask($input, $output, $question);
        }
        return $account;
    }

    protected function _initConfig()
    {
        $this->bitBucketConfiguration = new BitBucketConfiguration();
    }
}
