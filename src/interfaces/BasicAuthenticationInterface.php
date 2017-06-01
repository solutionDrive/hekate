<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     16:02
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\interfaces;

/**
 * Interface BasicAuthenticationInterface
 * @package sd\hekate\interfaces
 */
interface BasicAuthenticationInterface extends AuthenticationInterface
{
    public function setUserName($username);
}