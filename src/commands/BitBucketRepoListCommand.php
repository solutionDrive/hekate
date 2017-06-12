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

use Bitbucket\API\Repositories;
use sd\hekate\config\BitBucketConfiguration;
use sd\hekate\lib\BitbucketRepositoryList;
use sd\hekate\lib\HekateCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class BitBucketRepoListCommand extends AbstractBitbucketCommand
{
    /** int */
    const BITBUCKET_DEFAULT_LIFETIME = 360;

    /** string */
    const BITBUCKET_CACHE_DIRECTORY = __DIR__ . '/../../cache';

    /** @var  string */
    protected $account;
    /** @var  string */
    protected $username;
    /** @var  string */
    protected $password;
    /** @var  bool */
    protected $forceQuestions;

    /**
     * Configure the commad
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
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Alternative config file for your bitbucket-settings', realpath(BitBucketConfiguration::BITBUCKET_CONFIG_FILE_LOCATION))
        ;

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToConfig = $input->getOption('config');
        $this->_initConfig($pathToConfig);

        $this->forceQuestions = (bool)$input->getOption('ask-questions');

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $projectKey = $input->getOption('projectkey');

        $this->username   = $this->_getUserName($input, $output, $helper);
        $this->password   = $this->_getPassword($input, $output, $helper);
        $this->account    = $this->_getAccount($input, $output, $helper);

        $repoInfo = $this->_getInformationOfRepositories($projectKey);

        $table = new Table($output);
        $table->setHeaders(['name', 'project', 'slug']);
        $table->setRows($repoInfo);
        $table->render();
    }

    /**
     * @return BitbucketRepositoryList
     */
    protected function _createRepositoryList(): BitbucketRepositoryList
    {
        $repositoryList = $this->_getBitbucketRepositoryListService();
        $repositoryList->setCredentials($this->username, $this->password);
        $repositoryList->createPager($this->account);
        return $repositoryList;
    }

    /**
     * @param string $projectKey
     * @return mixed
     */
    protected function _getInformationOfRepositories($projectKey)
    {
        $repositoryList = $this->_createRepositoryList();

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
        $this->username = $input->getOption('username');
        if ($this->_needToAskForUsername()) {
            $question = new Question('Please enter your username: ');
            $this->username = $helper->ask($input, $output, $question);
        }
        return $this->username;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Helper          $helper
     * @return string
     */
    protected function _getPassword(InputInterface $input, OutputInterface $output, $helper): string
    {
        $this->password = $input->getOption('password');
        if ($this->_needToAskForPassword()) {
            $question = new Question('Please enter your password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $this->password = $helper->ask($input, $output, $question);
        }
        return $this->password;
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
        return $this->forceQuestions || (null === $this->account && $this->_accountIsNotInConfig());
    }

    /**
     * @return bool
     */
    protected function _accountIsNotInConfig(): bool
    {
        return empty($this->account = $this->bitBucketConfiguration->getAccountName());
    }

    /**
     * @return bool
     */
    protected function _needToAskForUsername(): bool
    {
        return $this->forceQuestions || (empty($this->username) && empty($this->username = $this->bitBucketConfiguration->getUserName()));
    }

    /**
     * @return bool
     */
    protected function _needToAskForPassword(): bool
    {
        return $this->forceQuestions || (empty($this->password) && empty($this->password = $this->bitBucketConfiguration->getPassword()));
    }
}
