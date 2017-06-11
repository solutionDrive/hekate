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
    /** @var  \org\bovigo\vfs\vfsStreamDirectory */
    protected $virtualRootDirectory;

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetUserName()
    {
        $this->_createTestSubjectWithVirtualConfigFile();
        self::assertEquals('test_user', $this->testSubject->getUserName());
    }

    public function testGetUserNameIsEmpty()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        self::assertEquals('', $this->testSubject->getUserName());
    }

    public function testGetPassword()
    {
        $this->_createTestSubjectWithVirtualConfigFile();
        self::assertEquals('test_password', $this->testSubject->getPassword());
    }


    public function testGetPasswordIsEmpty()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        self::assertEquals('', $this->testSubject->getPassword());
    }

    public function testGetAccount()
    {
        $this->_createTestSubjectWithVirtualConfigFile();
        self::assertEquals('test_account', $this->testSubject->getAccountName());
    }

    public function testGetAccountIsEmpty()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        self::assertEquals('', $this->testSubject->getAccountName());
    }

    public function testSetOnlyUsername()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        $this->testSubject->setUserName('test_user');
        $this->testSubject->save();

        $expectedConfig =
'bitbucket:
    username: test_user
';

        $this->assertEquals($expectedConfig, file_get_contents($this->virtualRootDirectory->url().'/bitbucket.yml'));
    }


    public function testSetOnlyPassword()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        $this->testSubject->setPassword('test_password');
        $this->testSubject->save();

        $expectedConfig =
            'bitbucket:
    password: test_password
';

        $this->assertEquals($expectedConfig, file_get_contents($this->virtualRootDirectory->url().'/bitbucket.yml'));
    }


    public function testSetOnlyAccountName()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        $this->testSubject->setAccountName('test_account');
        $this->testSubject->save();

        $expectedConfig =
            'bitbucket:
    account: test_account
';

        $this->assertEquals($expectedConfig, file_get_contents($this->virtualRootDirectory->url().'/bitbucket.yml'));
    }

    public function testSetUsernameAndPassword()
    {
        $this->_createTestSubjectWithEmptyVirtualConfigFile();
        $this->testSubject->setUserName('test_user');
        $this->testSubject->setPassword('test_password');
        $this->testSubject->save();

        $expectedConfig =
            'bitbucket:
    username: test_user
    password: test_password
';
        $this->assertEquals($expectedConfig, file_get_contents($this->virtualRootDirectory->url().'/bitbucket.yml'));

    }

    /**
     *
     */
    protected function _createTestSubjectWithVirtualConfigFile()
    {
        $this->virtualRootDirectory = vfsStream::setup('/');

        vfsStream::newFile('bitbucket.yml')->at($this->virtualRootDirectory)->withContent(
            'bitbucket:
                username: test_user
                password: test_password
                account: test_account'
        );
        $this->testSubject = new BitBucketConfiguration($this->virtualRootDirectory->url().'/bitbucket.yml');
    }

    protected function _createTestSubjectWithEmptyVirtualConfigFile()
    {
        $this->virtualRootDirectory = vfsStream::setup('/');

        vfsStream::newFile('bitbucket.yml')->at($this->virtualRootDirectory);
        $this->testSubject = new BitBucketConfiguration($this->virtualRootDirectory->url().'/bitbucket.yml');
    }
}
