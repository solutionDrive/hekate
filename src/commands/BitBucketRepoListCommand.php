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

use sd\hekate\config\BitBucketConfiguration;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BitBucketRepoListCommand
 * @package sd\hekate\commands
 */
class BitBucketRepoListCommand extends AbstractBitbucketCommand
{
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
        $this->forceQuestions = (bool)$input->getOption('ask-questions');

        $pathToConfig = $input->getOption('config');
        $this->_initConfig($pathToConfig);

        $this->_loadCredentials($input, $output);

        $projectKey = $input->getOption('projectkey');
        $repoInfo = $this->_getInformationOfRepositories($projectKey);

        $table = new Table($output);
        $table->setHeaders(['name', 'project', 'slug']);
        $table->setRows($repoInfo);
        $table->render();
    }

    /**
     * @return bool
     */
    protected function _needToAskForAccount(): bool
    {
        return $this->forceQuestions || parent::_needToAskForAccount();
    }

    /**
     * @return bool
     */
    protected function _needToAskForUsername(): bool
    {
        return $this->forceQuestions || parent::_needToAskForUsername();
    }

    /**
     * @return bool
     */
    protected function _needToAskForPassword(): bool
    {
        return $this->forceQuestions || parent::_needToAskForPassword();
    }
}
