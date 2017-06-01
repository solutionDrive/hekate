<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     14:39
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\services;

use sd\hekate\interfaces\BasicAuthenticationInterface;

/**
 * Class BitbucketAuthenticationInterface
 * @package sd\hekate\services
 */
class BitbucketAuthentication implements BasicAuthenticationInterface
{
    /** @var  string */
    protected $username;

    /**
     * @param string $username
     */
    public function setUserName($username)
    {
        $this->username = $username;
    }
}