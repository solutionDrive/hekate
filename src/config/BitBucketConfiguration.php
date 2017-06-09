<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      07.06.17
 * @time:     22:55
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\config;

use Symfony\Component\Yaml\Yaml;

class BitBucketConfiguration
{


    /**
     * BitBucketConfiguration constructor.
     */
    public function __construct()
    {
        $this->config = Yaml::parse(file_get_contents(__DIR__.'/../../hekate.yml'));
    }

    /**
     * @return string
     */
    public function getUserName():string
    {

        if ($this->_hasConfigParameter('username')) {
            return $this->_getConfigParameter('username');
        }
        return '';
    }

    /**
     * @return string
     */
    public function getPassword():string
    {
        if ($this->_hasConfigParameter('password')) {
            return $this->_getConfigParameter('password');
        }
        return '';
    }

    /**
     * @return string
     */
    public function getAccountName():string
    {
        if ($this->_hasConfigParameter('account')) {
            return $this->_getConfigParameter('account');
        }
        return '';
    }


    /**
     * @param string $nameOfConfigParameter
     * @return bool
     */
    protected function _hasConfigParameter($nameOfConfigParameter):bool
    {
        return empty($this->config['bitbucket'][$nameOfConfigParameter]) === false;
    }

    /**
     * @param string $nameOfConfigParameter
     * @return mixed
     */
    protected function _getConfigParameter($nameOfConfigParameter)
    {
        return $this->config['bitbucket'][$nameOfConfigParameter];
    }
}