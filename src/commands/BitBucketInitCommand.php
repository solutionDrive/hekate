<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      07.06.17
 * @time:     22:40
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\commands;


use sd\hekate\config\BitBucketConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class BitBucketInitCommand
 * @package sd\hekate\commands
 */
class BitBucketInitCommand extends AbstractHekateCommand
{
    protected function configure()
    {
        $this
            ->setName('bitbucket:init')
            ->setDescription('Creates the config for interacting with Hekate')
            ->setHelp('Command to generate a configuration file interactive for all available settings')
            ->addOption('--force', '-f', InputOption::VALUE_NONE, 'Force config-file initialisation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_initConfig();

        $forceMode = $input->getOption('force');

        if (empty($forceMode)) {
            if (file_exists(realpath(BitBucketConfiguration::BITBUCKET_CONFIG_FILE_LOCATION))) {
                $output->writeln('<info>There is already a Config File in Place in '
                    .realpath(BitBucketConfiguration::BITBUCKET_CONFIG_FILE_LOCATION).
                    ' - use --force to create the file anyway or edit it manually</info>');
                exit();
            }
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $this->_askAccount($input, $output, $helper);
        $this->_askUsername($input, $output, $helper);
        $this->_askPassword($input, $output, $helper);

        $this->_save();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $helper
     */
    protected function _askAccount(InputInterface $input, OutputInterface $output, $helper)
    {
        $question = new Question('Your Bitbucket Account: ');
        $account = $helper->ask($input, $output, $question);
        $this->bitBucketConfiguration->setAccountName($account);
    }

    protected function _askUsername($input, $output, $helper)
    {
        $question = new Question('Your Bitbucket Username: ');
        $username = $helper->ask($input, $output, $question);
        $this->bitBucketConfiguration->setUserName($username);
    }

    protected function _askPassword($input, $output, $helper)
    {
        $question = new Question('Your Bitbucket Password - no encryption yet so watch out!!: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);
        $this->bitBucketConfiguration->setPassword($password);
    }

    protected function _save()
    {
        $this->bitBucketConfiguration->save();
    }


}