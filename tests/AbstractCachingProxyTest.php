<?php

/**
 * This file is part of secra/CachingProxy.
 *
 * (c) Sebastian Krüger <krueger@secra.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace secra\CachingProxy\Tests;

use org\bovigo\vfs\vfsStream;
use secra\CachingProxy\AbstractCachingProxyMockClass;
use secra\CachingProxy\AbstractCachingProxyMockClassConstructor;

// include Testclasses
require_once 'AbstractCachingProxyMockClass.php';
require_once 'AbstractCachingProxyMockClassConstructor.php';

/**
 * CachingProxyTest
 *
 * @category test
 * @package de.secra.cachingproxy
 * @author Sebastian Krüger <krueger@secra.de>
 * @copyright 2014 Sebastian Krüger
 * @license http://www.opensource.org/licenses/MIT The MIT License
 */
class AbstractCachingProxyTest extends \PHPUnit\Framework\TestCase
{
    /*
     * @type AbstractCachingProxy
    */
    private $cachingproxy;

    /*
     * @type vfsStreamDirectory
    */
    protected $vfsroot;

    public function setUp() : void
    {
        // Set vfs Testfilesystem
        $this->vfsroot = vfsStream::setup('testroot');

        // build up structure with testfiles
        $vfsstructure = array(
            'testfiles' => array(
                'testfile1.test' => 'function testfunction() { var d1 = document.getElementById( "hitme" ); }',
                'testfile2.test' => 'function testfuncion2(demo) { colorDiv( var d2 = document.getElementById( "bluebox" ); }',
                'testfile1.min.test' => 'function testfunction(){var d1=document.getElementById("hitme");}',
            ),
            'cache' => array()
        );

        // fill structure below the vfs testdir
        vfsStream::create($vfsstructure, $this->vfsroot);

        // Set defined Filetime for all files
        // TODO: Think how to workaround for testing phpversion 5.3 because of touch function
        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile1.test');
        touch($testStreamUrl,1393628400,1393628400);

        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile2.test');
        touch($testStreamUrl,1393028400,1393028400);

        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile1.min.test');
        touch($testStreamUrl,1392628400,1392628400);

        $testStreamUrl=vfsStream::url('testroot');
        // instanciate teststub class for abstract class
        $this->cachingproxy = new AbstractCachingProxyMockClass($testStreamUrl."/","cache/");
    }


    /**
     * @test
     * @covers secra\CachingProxy\AbstractCachingProxy::__construct()
     */
    public function testConstruct()
    {
        $testStreamUrl=vfsStream::url('testroot');
        $brandNewTestObject = new AbstractCachingProxyMockClassConstructor($testStreamUrl,"cache");

        // check values
        $this->assertEquals("vfs://testroot/", $brandNewTestObject->getDocrootpath());
        $this->assertEquals("vfs://testroot/cache/", $brandNewTestObject->getCachepath());
        $this->assertEquals("/cache/", $brandNewTestObject->getRelCachepath());
    }

    /**
     * @test
     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()
     */
    public function testAddFileExtern()
    {
        // Add Files with different protocol definations
        $this->cachingproxy->addFile("https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");
        $this->cachingproxy->addFile("http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");
        $this->cachingproxy->addFile("//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");

        // return all extern files
        $fileset = $this->cachingproxy->getIncludeFileset();

        // count the list of files
        $this->assertCount(3, $fileset);
    }

    /**
     * @test
     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()
     */
    public function testAddFileIntern()
    {
        // Add different files on vfsStream filesystem
        $this->cachingproxy->addFile("testfiles/testfile1.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");

        // return all extern files
        $fileset = $this->cachingproxy->getInternFilelist();

        $this->assertCount(2, $fileset);

        $this->assertContains("vfs://testroot/testfiles/testfile1.min.test",$fileset);
        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);

        $this->assertNotContains("vfs://testroot/testfiles/testfile1.test",$fileset);
    }

    /**
     * @test
     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()
     */
    public function testAddFileInternWithMinPathDouble()
    {
        // Add different files on vfsStream filesystem
        // the file testfile1.test was added but detected as minified version
        $this->cachingproxy->addFile("testfiles/testfile1.test");
        $this->cachingproxy->addFile("testfiles/testfile1.min.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");

        // return all extern files
        $fileset = $this->cachingproxy->getInternFilelist();

        $this->assertCount(3, $fileset);

        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);
        $this->assertContains("vfs://testroot/testfiles/testfile1.min.test",$fileset);

        $this->assertNotContains("vfs://testroot/testfiles/testfile1.test",$fileset);
    }

    /**
     * @test
     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()
     */
    public function testAddFileInternNotExistingFile()
    {
        // Add different files on vfsStream filesystem
        // the file testfile1.test was added but detected as minified version
        $this->cachingproxy->addFile("testfiles/testfile2.test");
        $this->cachingproxy->addFile("testfiles/testfile3.test");

        // return all extern files
        $fileset = $this->cachingproxy->getInternFilelist();

        $this->assertCount(1, $fileset);

        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);

        $this->assertNotContains("vfs://testroot/testfiles/testfile3.test",$fileset);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnEmptySet()
    {
        // Add nothing and get empty list
        $fileset = $this->cachingproxy->getIncludeFileset();
        $this->assertCount(0, $fileset);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnOnlyExternFiles()
    {
        // add files with different protocol definations
        $urllist = array();
        $urllist[] = "https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY0&sensor=FALSE";
        $urllist[] = "http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY1&sensor=FALSE";
        $urllist[] = "//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY2&sensor=FALSE";
        foreach($urllist as $url) {
            $this->cachingproxy->addFile($url);
        }

        $fileset = $this->cachingproxy->getIncludeFileset();

        // see if count fits first
        $this->assertCount(3, $fileset);

        // see if we get them back by url string
        foreach($urllist as $url) {
            $this->assertContains($url, $fileset);
        }
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnDoubleExternFiles()
    {
        // add same file more then one time
        $urllist = array();
        $urllist[] = "http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY2&sensor=FALSE";
        $urllist[] = "http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY2&sensor=FALSE";
        $urllist[] = "http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY2&sensor=FALSE";
        $urllist[] = "http://www.somedomain.com/api/file.js";
        $urllist[] = "http://www.somedomain.com/api/file.js";

        foreach($urllist as $url) {
            $this->cachingproxy->addFile($url);
        }

        $fileset = $this->cachingproxy->getIncludeFileset();

        // See if count fits first
        $this->assertCount(2, $fileset);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnOnlyInternFilesDebugmodeOff()
    {
        // disable debugmode
        $this->cachingproxy->disableDebugmode();

        // add files with different protocol definations
        $urllist = array();

        $urllist[] = "testfiles/testfile1.test";
        $urllist[] = "testfiles/testfile2.test";

        foreach($urllist as $url) {
            $this->cachingproxy->addFile($url);
        }

        $fileset = $this->cachingproxy->getIncludeFileset();

        // see if count fits first one combined file
        $this->assertCount(1, $fileset);
        $this->assertContains("cache/edf5b187774d9b4ef2333670ed698c46.test", $fileset);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnOnlyInternFilesDebugmodeOn()
    {
        // enable debugmode
        $this->cachingproxy->enableDebugmode();

        // add some intern files
        $urllist = array();

        $urllist[] = "testfiles/testfile1.test";
        $urllist[] = "testfiles/testfile2.test";

        foreach($urllist as $url) {
            $this->cachingproxy->addFile($url);
        }

        $fileset = $this->cachingproxy->getIncludeFileset();

        // see if count fits first, all files will return in debugmode
        $this->assertCount(2, $fileset);

        // the testfile1 will be processed as testfile1.min.test
        $this->assertContains("/testfiles/testfile1.min.test", $fileset);
        $this->assertContains("/testfiles/testfile2.test", $fileset);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()
     */
    public function testGetIncludeFilesetReturnDoubleInternFilesDebugmodeOn()
    {
        // enable debugmode
        $this->cachingproxy->enableDebugmode();

        // add files some more times
        $urllist = array();

        $urllist[] = "testfiles/testfile1.test";
        $urllist[] = "testfiles/testfile1.min.test";
        $urllist[] = "testfiles/testfile2.test";
        $urllist[] = "testfiles/testfile2.test";
        $urllist[] = "testfiles/testfile2.test";

        foreach($urllist as $url) {
            $this->cachingproxy->addFile($url);
        }

        $fileset = $this->cachingproxy->getIncludeFileset();

        // see if count fits first, all files will return in debugmode
        $this->assertCount(2, $fileset);

        // the testfile1 will be processed as testfile1.min.test
        $this->assertContains("/testfiles/testfile1.min.test", $fileset);
        $this->assertContains("/testfiles/testfile2.test", $fileset);
    }

    /**
     * @test
     *
     * @covers secra\Cachingproxy\AbstractCachingProxy::enableDebugmode()
     *
     */
    public function testEnableDebugmode()
    {
        // debugmode is disabled by default
        $this->assertFalse($this->cachingproxy->getDebugMode());

        // enable it and test return value
        $this->assertNull($this->cachingproxy->enableDebugmode());

        // test if mode is set
        $this->assertTrue($this->cachingproxy->getDebugMode());
    }

    /**
     * @test
     *
     * @covers secra\Cachingproxy\AbstractCachingProxy::disableDebugmode()
     *
     */
    public function testDisableDebugmode()
    {
        // debugmode is disabled by default
        $this->assertFalse($this->cachingproxy->getDebugMode());

        // enable debugmode
        $this->cachingproxy->enableDebugmode();

        // unset debugmode and test once more
        $this->assertNull($this->cachingproxy->disableDebugmode());
        $this->assertFalse($this->cachingproxy->getDebugMode());
    }

    /**
     * @test
     *
     * @covers secra\Cachingproxy\AbstractCachingProxy::setWebserverRootPath()
     *
     */
    public function testSetWebserverRootPath()
    {
        // build reflection of protected function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'setWebserverRootPath');
        $method->setAccessible(true);

        // set nonexisting rootpath
        $testStreamUrl=vfsStream::url('testroot');
        $this->assertFalse($method->invoke($this->cachingproxy, $testStreamUrl."/nonexitstingpath"));
        $this->assertNull($this->cachingproxy->getDocrootpath());

        // set existing rootpath
        $testStreamUrl=vfsStream::url('testroot');
        $this->assertTrue($method->invoke($this->cachingproxy, $testStreamUrl));
        $this->assertEquals("vfs://testroot/", $this->cachingproxy->getDocrootpath());

        $this->assertTrue($method->invoke($this->cachingproxy, $testStreamUrl."/"));
        $this->assertEquals("vfs://testroot/", $this->cachingproxy->getDocrootpath());
    }

    /**
     * @test
     *
     * @covers secra\Cachingproxy\AbstractCachingProxy::setCachepath()
     *
     */
    public function testSetCachepath()
    {
        // build reflection of protected function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'setCachepath');
        $method->setAccessible(true);

        // set nonexisting cachepath
        $this->assertFalse($method->invoke($this->cachingproxy, "/nonexitstingpath"));

        // set existing cachepath
        $this->assertTrue($method->invoke($this->cachingproxy, "cache"));

        // the double trailing slash is a tribute to mock the realpath function
        $this->assertEquals("vfs://testroot/cache/", $this->cachingproxy->getCachepath());
        $this->assertEquals("/cache/", $this->cachingproxy->getRelCachepath());

        $this->assertTrue($method->invoke($this->cachingproxy, "cache/"));
        $this->assertEquals("vfs://testroot/cache/", $this->cachingproxy->getCachepath());
        $this->assertEquals("/cache/", $this->cachingproxy->getRelCachepath());
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentReturnDefaultSameValue()
    {
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'modifyFilecontent');
        $method->setAccessible(true);

        $this->assertEquals("abcdefghijklmopqöäü12345/€", $method->invokeArgs($this->cachingproxy,array("abcdefghijklmopqöäü12345/€","/")));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::makeMinifiPath()
     */
    public function testMakeMinifiPath()
    {
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'makeMinifiPath');
        $method->setAccessible(true);

        $testpath = "/var/www/htdocs/css/file.css";
        $this->assertEquals("/var/www/htdocs/css/file.min.css", $method->invoke(new AbstractCachingProxyMockClass('/','cache'), $testpath));

        $testpath = "/var/www/htdocs/css/file.js";
        $this->assertEquals("/var/www/htdocs/css/file.min.js", $method->invoke(new AbstractCachingProxyMockClass('/','cache'), $testpath));

        $testpath = "/var/www/htdocs/css/here.is.a.js";
        $this->assertEquals("/var/www/htdocs/css/here.is.a.min.js", $method->invoke(new AbstractCachingProxyMockClass('/','cache'), $testpath));

        $testpath = "/var/www/htdocs/css/file..js";
        $this->assertEquals("/var/www/htdocs/css/file..min.js", $method->invoke(new AbstractCachingProxyMockClass('/','cache'), $testpath));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()
     */
    public function testGetFileSignatureReturnNullOnEmtyInternFilelist()
    {
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'getCacheFile');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($this->cachingproxy));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()
     */
    public function testGetFileSignatureCreateCacheFileIfNotExists()
    {
        // TODO: This test has bad functional test character -> solution eleminate the dependecy on calculateFileSignature()
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'getCacheFile');
        $method->setAccessible(true);

        // add testfiles
        $this->cachingproxy->addFile("testfiles/testfile1.min.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");

        // check there are no files in cache folder until now
        $directory = array_diff(scandir("vfs://testroot/cache/"), array('.', '..'));
        $this->assertCount(0, $directory);

        $this->assertEquals("cache/edf5b187774d9b4ef2333670ed698c46.test", $method->invoke($this->cachingproxy));

        // check if gz and normal file version build in cache dir
        $directory = array_diff(scandir("vfs://testroot/cache/"), array('.', '..'));

        $this->assertCount(2, $directory);
        $this->assertContains("edf5b187774d9b4ef2333670ed698c46.test", $directory);
        $this->assertContains("edf5b187774d9b4ef2333670ed698c46.test.gz", $directory);

        // check filesize of files
        $this->assertEquals(155, filesize("vfs://testroot/cache/edf5b187774d9b4ef2333670ed698c46.test"));
        $this->assertEquals(128, filesize("vfs://testroot/cache/edf5b187774d9b4ef2333670ed698c46.test.gz"));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()
     */
    public function testGetFileSignatureReturnCacheFileIfAlreadyExists()
    {
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'getCacheFile');
        $method->setAccessible(true);

        // add testfiles
        $this->cachingproxy->addFile("testfiles/testfile1.min.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");

        // put expected resulting cachefile into folder
        $vfsstructure = array(
            'cache' => array(
                'edf5b187774d9b4ef2333670ed698c46.test' => 'content dosent matter now',
                'edf5b187774d9b4ef2333670ed698c46.test.gz' => 'content dosent matter now'
            )
        );

        // fill structure below the vfs testdir
        vfsStream::create($vfsstructure, $this->vfsroot);

        // Filename and path to unzipped file must be return
        $this->assertEquals("cache/edf5b187774d9b4ef2333670ed698c46.test", $method->invoke($this->cachingproxy));
    }

    // TODO: Think about testcase, that test, if cached file will not be create but returned, if already exits

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()
     */
 /*
    exclude test for now, because of non catchable error

    public function testGetFileSignatureErrorOnCreateCacheFileIfNotExists()
    {
        // Build reflection of private Function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'getCacheFile');
        $method->setAccessible(true);

        // Add Testfiles
        $this->cachingproxy->addFile("testfiles/testfile1.min.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");

        // make cachefoolder read only
        chmod("vfs://testroot/cache/",555);

        $method->invoke($this->cachingproxy);
    } */

    /**
     * @test
     * @covers secra\Cachingproxy\AbstractCachingProxy::calculateFileSignature()
     */
    public function testCalculateFileSignature()
    {
        // build reflection of private function
        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyMockClass', 'calculateFileSignature');
        $method->setAccessible(true);

        // add 3 intern files to testobject
        $this->cachingproxy->addFile("testfiles/testfile1.test");
        $this->cachingproxy->addFile("testfiles/testfile2.test");
        $this->cachingproxy->addFile("testfiles/testfile1.min.test");

        $this->assertEquals("3a4f58bfd0e56d751b3910429213824b",$method->invoke($this->cachingproxy));
    }
}