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


use sd\hekate\config\BitBucketConfiguration;
use Symfony\Component\Console\Command\Command;

class AbstractBitbucketCommand extends Command
{
    /** @var  BitBucketConfiguration */
    protected $bitBucketConfiguration;

    protected function _initConfig($pathToConfigFile = BitBucketConfiguration::BITBUCKET_CONFIG_FILE_LOCATION)
    {
        $this->bitBucketConfiguration = new BitBucketConfiguration($pathToConfigFile);
    }
}
