<?php
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use sd\hekate\config\BitBucketConfiguration;

/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      11.06.17
 * @time:     09:19
 * @copyright 2017 solutionDrive GmbH
 */
class BitBucketConfigurationTest extends TestCase
{
    /** @var  BitBucketConfiguration */
    protected $testSubject;

    public function setUp()
    {
        $rootDirectory = vfsStream::setup('/');
        vfsStream::newFile('bitbucket.yml')->at($rootDirectory)->withContent(
            'bitbucket:
                username: test_user
                password: test_password
                account: test_account'
        );

        $this->testSubject = new BitBucketConfiguration($rootDirectory->url().'/bitbucket.yml');
        parent::setUp();
    }

    public function testGetUserName()
    {
        self::assertEquals('test_user', $this->testSubject->getUserName());
    }

    public function testGetPassword()
    {
        self::assertEquals('test_password', $this->testSubject->getPassword());
    }

    public function testGetAccount()
    {
        self::assertEquals('test_account', $this->testSubject->getAccountName());
    }
}
