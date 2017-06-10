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
use sd\hekate\lib\HekateCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class BitBucketRepoListCommand extends AbstractHekateCommand
{
    const BITBUCKET_DEFAULT_LIFETIME = 360;
    const BITBUCKET_CACHE_DIRECTORY = __DIR__ . '/../../cache';
    /** @var  BitBucketConfiguration */
    protected $account;
    protected $forceQuestions;

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
            ->addOption('ask-questions', 'aq', InputOption::VALUE_NONE, 'Give Credentials on commandline prompt')
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

        $this->forceQuestions = (bool)$input->getOption('ask-questions');

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
        $repositoryList = $this->_getBitbucketRepositoryListService();
        $repositoryList->setCredentials($username, $password);
        $repositoryList->createPager($account);
        return $repositoryList;
    }

    /**
     * @param $projectKey
     * @param BitbucketRepositoryList $repositoryList
     * @return mixed
     */
    protected function _getInformationOfRepositories($projectKey, $repositoryList)
    {
        if (empty($projectKey) === false) {
            $repoInfo = $repositoryList->getAllForProjectKey($projectKey, $this->account);
        } else {
            $repoInfo = $repositoryList->getAll($this->account);
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
        if ($this->_needToAskForUsername($username)) {
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
        if ($this->_needToAskForPassword($password)) {
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
    protected function _getAccount(InputInterface $input, OutputInterface $output, $helper)
    {
        $this->account = $input->getOption('account');
        if ($this->_needToAskForAccount()) {
            $question = new Question('Please enter the name of your Bitbucket Account: ');
            $this->account = $helper->ask($input, $output, $question);
        }
        return $this->account;
    }

    /**
     * @return BitbucketRepositoryList
     */
    protected function _getBitbucketRepositoryListService(): BitbucketRepositoryList
    {
        $repositoryList = new BitbucketRepositoryList(
            new Repositories(),
            new HekateCache(
                new FilesystemAdapter(
                    BitbucketRepositoryList::BITBUCKET_CACHE_KEY,
                    self::BITBUCKET_DEFAULT_LIFETIME,
                    self::BITBUCKET_CACHE_DIRECTORY)
            )
        );
        return $repositoryList;
    }

    /**
     * @return bool
     */
    protected function _needToAskForAccount(): bool
    {
        return $this->forceQuestions || (is_null($this->account) && $this->_isNotInConfig());
    }

    /**
     * @return bool
     */
    protected function _isNotInConfig(): bool
    {
        return empty($this->account = $this->bitBucketConfiguration->getAccountName());
    }

    /**
     * @param $username
     * @return bool
     */
    protected function _needToAskForUsername(&$username): bool
    {
        return $this->forceQuestions || (empty($username) && empty($username = $this->bitBucketConfiguration->getUserName()));
    }

    /**
     * @param $password
     * @return bool
     */
    protected function _needToAskForPassword(&$password): bool
    {
        return $this->forceQuestions || (empty($password) && empty($password = $this->bitBucketConfiguration->getPassword()));
    }
}
