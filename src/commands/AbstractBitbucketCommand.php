<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      09.06.17
 * @time:     23:32
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\commands;


use Bitbucket\API\Repositories;
use sd\hekate\config\BitBucketConfiguration;
use sd\hekate\lib\BitbucketRepositoryList;
use sd\hekate\lib\HekateCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class AbstractBitbucketCommand
 * @package sd\hekate\commands
 */
class AbstractBitbucketCommand extends Command
{
    /** @var  BitBucketConfiguration */
    protected $bitBucketConfiguration;

    /** int */
    const BITBUCKET_DEFAULT_LIFETIME = 86400; // 24 - hours

    /** string */
    const BITBUCKET_CACHE_DIRECTORY = __DIR__ . '/../../cache';

    /** @var  string */
    protected $account;
    /** @var  string */
    protected $username;
    /** @var  string */
    protected $password;

    protected function _initConfig($pathToConfigFile = BitBucketConfiguration::BITBUCKET_CONFIG_FILE_LOCATION)
    {
        $this->bitBucketConfiguration = new BitBucketConfiguration($pathToConfigFile);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function _loadCredentials(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $this->username = $this->_getUserName($input, $output, $helper);
        $this->password = $this->_getPassword($input, $output, $helper);
        $this->account = $this->_getAccount($input, $output, $helper);
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
     * @return bool
     */
    protected function _needToAskForAccount(): bool
    {
        return (null === $this->account && $this->_accountIsNotInConfig());
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
        return (empty($this->username) && empty($this->username = $this->bitBucketConfiguration->getUserName()));
    }

    /**
     * @return bool
     */
    protected function _needToAskForPassword(): bool
    {
        return (empty($this->password) && empty($this->password = $this->bitBucketConfiguration->getPassword()));
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
}
