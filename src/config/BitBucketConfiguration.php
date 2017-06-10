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
    const BITBUCKET_CONFIG_FILE_LOCATION = __DIR__ . '/../../hekate.yml';
    const BITBUCKET_CONFIG_KEY = 'bitbucket';


    /**
     * BitBucketConfiguration constructor.
     */
    public function __construct()
    {
        $this->config = Yaml::parse(file_get_contents(self::BITBUCKET_CONFIG_FILE_LOCATION));
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

    public function setUserName($username)
    {
        $this->_setConfigParameter('username', $username);
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
     * @todo evtl hier das pw mit dem public key des rechners verschlusseln
     * @param $password
     */
    public function setPassword($password)
    {
        $this->_setConfigParameter('password', $password);
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


    public function setAccountName($accountName)
    {
        $this->_setConfigParameter('account', $accountName);
    }

    /**
     * @param string $nameOfConfigParameter
     * @return bool
     */
    protected function _hasConfigParameter($nameOfConfigParameter):bool
    {
        return empty($this->config[self::BITBUCKET_CONFIG_KEY][$nameOfConfigParameter]) === false;
    }

    /**
     * @param string $nameOfConfigParameter
     * @return mixed
     */
    protected function _getConfigParameter($nameOfConfigParameter)
    {
        return $this->config[self::BITBUCKET_CONFIG_KEY][$nameOfConfigParameter];
    }

    /**
     * @param string $nameOfConfigParameter
     * @param string $valueOfConfigParameter
     */
    protected function _setConfigParameter($nameOfConfigParameter, $valueOfConfigParameter)
    {
        $this->config[self::BITBUCKET_CONFIG_KEY][$nameOfConfigParameter] = $valueOfConfigParameter;
    }

    public function save()
    {
        file_put_contents(self::BITBUCKET_CONFIG_FILE_LOCATION, Yaml::dump($this->config));
    }
}