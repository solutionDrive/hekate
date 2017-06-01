<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     15:57
 * @copyright 2017 solutionDrive GmbH
 */

use sd\hekate\services\BitbucketAuthentication;
use PHPUnit\Framework\TestCase;

class BitbucketAuthenticationTest extends TestCase
{
    /** @var  BitbucketAuthentication */
    protected $testSubject;

    protected function setUp()
    {
        $this->testSubject = new BitbucketAuthentication();
    }

    public function testSetUserName()
    {
        $this->testSubject->setUserName('my_user_name');
        self::assertAttributeEquals('my_user_name', 'username', $this->testSubject);
    }

    public function testSetPassword()
    {
        $this->testSubject->setPassword('my_secret_password');
        self::assertAttributeEquals('my_secret_password', 'password', $this->testSubject);
    }
}
