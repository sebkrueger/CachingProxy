<?php/** * This file is part of secra/CachingProxy. * * (c) Sebastian Krüger <krueger@secra.de> * * For the full copyright and license information, please view the LICENSE * file that was distributed with this source code. */namespace secra\CachingProxy\Tests;use org\bovigo\vfs\vfsStream;use secra\CachingProxy\AbstractCachingProxyTestClass;use secra\CachingProxy\AbstractCachingProxyTestConstructor;// include Testclassrequire_once 'AbstractCachingProxyTestClass.php';require_once 'AbstractCachingProxyTestConstructor.php';/** * CachingProxyTest * * @category test * @package de.secra.cachingproxy * @author Sebastian Krüger <krueger@secra.de> * @copyright 2014 Sebastian Krüger * @license http://www.opensource.org/licenses/MIT The MIT License */class AbstractCachingProxyTest extends \PHPUnit_Framework_TestCase{    /*     * @type AbstractCachingProxy    */    private $cachingproxy;    /*     * @type vfsStreamDirectory    */    protected $vfsroot;    public function setUp()    {        // Set vfs Testfilesystem        $this->vfsroot = vfsStream::setup('testroot');        // build up structure with testfiles        $vfsstructure = array(            'testfiles' => array(                'testfile1.test' => 'function testfunction() { var d1 = document.getElementById( "hitme" ); }',                'testfile2.test' => 'function testfuncion2(demo) { colorDiv( var d2 = document.getElementById( "bluebox" ); }',                'testfile1.min.test' => 'function testfunction(){var d1=document.getElementById("hitme");}',            ),            'cache' => array()        );        // fill structure below the vfs testdir        vfsStream::create($vfsstructure, $this->vfsroot);        // Set defined Filetime for all files        // TODO: Think how to workaround for testing phpversion 5.3 because of touch function        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile1.test');        touch($testStreamUrl,1393628400,1393628400);        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile2.test');        touch($testStreamUrl,1393028400,1393028400);        $testStreamUrl=vfsStream::url('testroot/testfiles/testfile1.min.test');        touch($testStreamUrl,1392628400,1392628400);        $testStreamUrl=vfsStream::url('testroot');        // instanciate teststub class for abstract class        $this->cachingproxy = new AbstractCachingProxyTestClass($testStreamUrl."/","cache/");    }    /**     * @test     * @covers secra\CachingProxy\AbstractCachingProxy::__construct()     */    public function testConstruct()    {        $testStreamUrl=vfsStream::url('testroot');        $brandNewTestObject = new AbstractCachingProxyTestConstructor($testStreamUrl,"cache");        // check values        $this->assertEquals("vfs://testroot/", $brandNewTestObject->getDocrootpath());        $this->assertEquals("vfs://testroot/cache/", $brandNewTestObject->getCachepath());        $this->assertEquals("/cache/", $brandNewTestObject->getRelCachepath());    }    /**     * @test     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()     */    public function testAddFileExtern()    {        // Add Files with different protocol definations        $this->cachingproxy->addFile("https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");        $this->cachingproxy->addFile("http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");        $this->cachingproxy->addFile("//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");        // return all extern files        $fileset = $this->cachingproxy->getIncludeFileset();        // count the list of files        $this->assertCount(3, $fileset);    }    /**     * @test     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()     */    public function testAddFileIntern()    {        // Add different files on vfsStream filesystem        $this->cachingproxy->addFile("testfiles/testfile1.test");        $this->cachingproxy->addFile("testfiles/testfile2.test");        // return all extern files        $fileset = $this->cachingproxy->getInternFilelist();        $this->assertCount(2, $fileset);        $this->assertContains("vfs://testroot/testfiles/testfile1.min.test",$fileset);        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);        $this->assertNotContains("vfs://testroot/testfiles/testfile1.test",$fileset);    }    /**     * @test     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()     */    public function testAddFileInternWithMinPathDouble()    {        // Add different files on vfsStream filesystem        // the file testfile1.test was added but detected as minified version        $this->cachingproxy->addFile("testfiles/testfile1.test");        $this->cachingproxy->addFile("testfiles/testfile1.min.test");        $this->cachingproxy->addFile("testfiles/testfile2.test");        // return all extern files        $fileset = $this->cachingproxy->getInternFilelist();        $this->assertCount(3, $fileset);        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);        $this->assertContains("vfs://testroot/testfiles/testfile1.min.test",$fileset);        $this->assertNotContains("vfs://testroot/testfiles/testfile1.test",$fileset);    }    /**     * @test     * @covers secra\CachingProxy\AbstractCachingProxy::addFile()     */    public function testAddFileInternNotExistingFile()    {        // Add different files on vfsStream filesystem        // the file testfile1.test was added but detected as minified version        $this->cachingproxy->addFile("testfiles/testfile2.test");        $this->cachingproxy->addFile("testfiles/testfile3.test");        // return all extern files        $fileset = $this->cachingproxy->getInternFilelist();        $this->assertCount(1, $fileset);        $this->assertContains("vfs://testroot/testfiles/testfile2.test",$fileset);        $this->assertNotContains("vfs://testroot/testfiles/testfile3.test",$fileset);    }    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::addFile()     * @covers secra\Cachingproxy\AbstractCachingProxy::getIncludeFileset()     * @covers secra\CachingProxy\AbstractCachingProxy::getCacheFile()     */    public function testGetIncludeFilesets()    {        // TODO: Test Intern files        // Add nothing and get emty list        $fileset = $this->cachingproxy->getIncludeFileset();        $this->assertCount(0, $fileset);        // Add Files with different protocol definations        $urllist = array();        $urllist[] = "https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY0&sensor=FALSE";        $urllist[] = "http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY1&sensor=FALSE";        $urllist[] = "//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY2&sensor=FALSE";        foreach($urllist as $url) {            $this->cachingproxy->addFile($url);        }        $fileset = $this->cachingproxy->getIncludeFileset();        // See if count fitts first        $this->assertCount(3, $fileset);        // See if we get them back by url string        foreach($urllist as $url) {            $this->assertContains($url, $fileset);        }    }    /**     * @test     *     * @covers secra\Cachingproxy\AbstractCachingProxy::enableDebugmode()     * @covers secra\Cachingproxy\AbstractCachingProxy::disableDebugmode()     *     */    public function testDebugmode()    {        // TODO: split into different function to test different habbits        // debugmode is disabled as default        $this->assertFalse($this->cachingproxy->getDebugMode());        // enable it and test return value        $this->assertNull($this->cachingproxy->enableDebugmode());        // test if mode is set        $this->assertTrue($this->cachingproxy->getDebugMode());        // unset and test once more        $this->assertNull($this->cachingproxy->disableDebugmode());        $this->assertFalse($this->cachingproxy->getDebugMode());    }    /**     * @test     *     * @covers secra\Cachingproxy\AbstractCachingProxy::setWebserverRootPath()     *     */    public function testSetWebserverRootPath()    {        // Build reflection of protected function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'setWebserverRootPath');        $method->setAccessible(true);        // Set nonexisting rootpath        $testStreamUrl=vfsStream::url('testroot');        $this->assertFalse($method->invoke($this->cachingproxy, $testStreamUrl."/nonexitstingpath"));        $this->assertNull($this->cachingproxy->getDocrootpath());        // Set existing Rootpath        $testStreamUrl=vfsStream::url('testroot');        $this->assertTrue($method->invoke($this->cachingproxy, $testStreamUrl));        $this->assertEquals("vfs://testroot/", $this->cachingproxy->getDocrootpath());        $this->assertTrue($method->invoke($this->cachingproxy, $testStreamUrl."/"));        $this->assertEquals("vfs://testroot/", $this->cachingproxy->getDocrootpath());    }    /**     * @test     *     * @covers secra\Cachingproxy\AbstractCachingProxy::setCachepath()     *     */    public function testSetCachepath()    {        // Build reflection of protected function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'setCachepath');        $method->setAccessible(true);        // Set nonexisting cachepath        $this->assertFalse($method->invoke($this->cachingproxy, "/nonexitstingpath"));        // Set existing cachepath        $this->assertTrue($method->invoke($this->cachingproxy, "cache"));        // the double trailing slash is a tribute to mock the realpath function        $this->assertEquals("vfs://testroot/cache/", $this->cachingproxy->getCachepath());        $this->assertEquals("/cache/", $this->cachingproxy->getRelCachepath());        $this->assertTrue($method->invoke($this->cachingproxy, "cache/"));        $this->assertEquals("vfs://testroot/cache/", $this->cachingproxy->getCachepath());        $this->assertEquals("/cache/", $this->cachingproxy->getRelCachepath());    }    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::makeAbsolutPath()     */ /*    Comment because override the realpath function    public function testMakeAbsolutPath()    {        // Build reflection of private function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'makeAbsolutPath');        $method->setAccessible(true);        // You can't test this function via vfs file system : https://github.com/mikey179/vfsStream/wiki/Known-Issues        // and with real test file system you have fragile test because of different file systems        // but is is worth a test, if function return false on not existing path        $this->assertFalse($method->invoke(new AbstractCachingProxyTestClass("/","/cache"), "/this/path/wont/exists"));    } */    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::makeMinifiPath()     */    public function testMakeMinifiPath()    {        // Build reflection of private Function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'makeMinifiPath');        $method->setAccessible(true);        $testpath = "/var/www/htdocs/css/file.css";        $this->assertEquals("/var/www/htdocs/css/file.min.css", $method->invoke(new AbstractCachingProxyTestClass('/','cache'), $testpath));        $testpath = "/var/www/htdocs/css/file.js";        $this->assertEquals("/var/www/htdocs/css/file.min.js", $method->invoke(new AbstractCachingProxyTestClass('/','cache'), $testpath));        $testpath = "/var/www/htdocs/css/here.is.a.js";        $this->assertEquals("/var/www/htdocs/css/here.is.a.min.js", $method->invoke(new AbstractCachingProxyTestClass('/','cache'), $testpath));        $testpath = "/var/www/htdocs/css/file..js";        $this->assertEquals("/var/www/htdocs/css/file..min.js", $method->invoke(new AbstractCachingProxyTestClass('/','cache'), $testpath));    }    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()     */    public function testGetFileSignatureReturnNullOnEmtyInternFilelist()    {        // Build reflection of private Function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'getCacheFile');        $method->setAccessible(true);        $this->assertNull($method->invoke($this->cachingproxy));    }    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()     */    public function testGetFileSignatureCreateCacheFileIfNotExists()    {        // TODO: This test has bad functional test character -> solution eleminate the dependecy on calculateFileSignature()        // Build reflection of private Function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'getCacheFile');        $method->setAccessible(true);        // Add Testfiles        $this->cachingproxy->addFile("testfiles/testfile1.min.test");        $this->cachingproxy->addFile("testfiles/testfile2.test");        // Check there are no files in cache folder until now        $directory = scandir("vfs://testroot/cache/");        $this->assertCount(0,$directory);        $this->assertEquals("cache/edf5b187774d9b4ef2333670ed698c46.test", $method->invoke($this->cachingproxy));        // Check if gz and normal file version build in cache dir        $directory = scandir("vfs://testroot/cache/");        $this->assertCount(2, $directory);        $this->assertContains("edf5b187774d9b4ef2333670ed698c46.test", $directory);        $this->assertContains("edf5b187774d9b4ef2333670ed698c46.test.gz", $directory);        // Check File size of files        $this->assertEquals(155, filesize("vfs://testroot/cache/edf5b187774d9b4ef2333670ed698c46.test"));        $this->assertEquals(128, filesize("vfs://testroot/cache/edf5b187774d9b4ef2333670ed698c46.test.gz"));    }    // TODO: Think about testcase, that test, if cached file will not be create but returned, if already exits    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::getCacheFile()     */ /*    exclude test for now    public function testGetFileSignatureErrorOnCreateCacheFileIfNotExists()    {        // Build reflection of private Function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'getCacheFile');        $method->setAccessible(true);        // Add Testfiles        $this->cachingproxy->addFile("testfiles/testfile1.min.test");        $this->cachingproxy->addFile("testfiles/testfile2.test");        // make cachefoolder read only        chmod("vfs://testroot/cache/",555);        $method->invoke($this->cachingproxy);    } */    /**     * @test     * @covers secra\Cachingproxy\AbstractCachingProxy::calculateFileSignature()     */    public function testCalculateFileSignature()    {        // Build reflection of private Function        $method = new \ReflectionMethod('\secra\CachingProxy\AbstractCachingProxyTestClass', 'calculateFileSignature');        $method->setAccessible(true);        // Add 3 intern files to testobject        $this->cachingproxy->addFile("testfiles/testfile1.test");        $this->cachingproxy->addFile("testfiles/testfile2.test");        $this->cachingproxy->addFile("testfiles/testfile1.min.test");        $this->assertEquals("3a4f58bfd0e56d751b3910429213824b",$method->invoke($this->cachingproxy));    }}