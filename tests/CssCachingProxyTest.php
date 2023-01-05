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

use secra\CachingProxy\CssCachingProxy;
/**
 * CachingProxyTest
 *
 * @category test
 * @package de.secra.cachingproxy
 * @author Sebastian Krüger <krueger@secra.de>
 * @copyright 2014 Sebastian Krüger
 * @license http://www.opensource.org/licenses/MIT The MIT License
 */
class CssCachingProxyTest extends \PHPUnit\Framework\TestCase
{
    private $cachingproxy;

    public function setUp() : void
    {
        // instanciate testclass
        $this->cachingproxy = new CssCachingProxy("/","css/cache");
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::getCacheFileExtension()
     */
    public function testGetCacheFileExtension()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod('\secra\CachingProxy\CssCachingProxy', 'getCacheFileExtension');
        $method->setAccessible(true);

        // See if the extension match
        $this->assertEquals(".css", $method->invoke($this->cachingproxy));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::getIncludeHtml()
     */
    public function testGetIncludeHtml()
    {

        /// TODO: Now test baseclass functions, try to avoid this

        // See if we get html file
        $testHtml = $this->cachingproxy->getIncludeHtml();

        // First Test nothing to get out
        $this->assertEquals("", $testHtml);

        // Extern Test URL
        $externUrl = "http://www.example.com/test.css";

        //  Style Tag html
        $styleTagHtml = '<link rel="stylesheet" type="text/css" href="'.$externUrl.'" />'."\n";

        // Add the extern File to List
        $this->cachingproxy->addFile($externUrl);

        // get the extended scripttag
        $testHtml = $this->cachingproxy->getIncludeHtml();

        $this->assertStringEndsWith("\n", $testHtml);
        $this->assertEquals($styleTagHtml, $testHtml);
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentAbsolutUrls()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".bgImageVersion1 {\n";
        $csscontent .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion1 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentDotRelativeUrls()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".bgImageVersion2 {\n";
        $csscontent .= "     background-image: url(\"./img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion3 {\n";
        $csscontent .= "     background-image: url(./img/german_flag.jpg);\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion2 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion3 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentDotRelativeUrlsWithSpecialChars()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".fontVersion {\n";
        $csscontent .= "     src: url('./font/fontawesome-webfont.eot?#iefix&v=3.0.1');\n";
        $csscontent .= "}";

        $csscontentexpected  = ".fontVersion {\n";
        $csscontentexpected .= "     src: url(\"/demo/css/css_framework_abc/font/fontawesome-webfont.eot?#iefix&v=3.0.1\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentOneDoubleDotRelativeUrls()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".bgImageVersion4 {\n";
        $csscontent .= "     background-image: url(\"../css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion5 {\n";
        $csscontent .= "     background-image: url(../css_framework_abc/img/german_flag.jpg);\n";
        $csscontent .= "}\n";
        $csscontent .= ".fontVersion {\n";
        $csscontent .= "     src: url('../font/fontawesome-webfont.eot?#iefix&v=3.0.1');\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion4 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion5 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n";
        $csscontentexpected .= ".fontVersion {\n";
        $csscontentexpected .= "     src: url(\"/demo/css/font/fontawesome-webfont.eot?#iefix&v=3.0.1\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }

    /**
 * @test
 * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
 */
    public function testModifyFilecontentTwoDoubleDotRelativeUrls()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".bgImageVersion6 {\n";
        $csscontent .= "     background-image: url(\"../../css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion7 {\n";
        $csscontent .= "     background-image: url(../../css/css_framework_abc/img/german_flag.jpg);\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion6 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion7 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::modifyFilecontent()
     */
    public function testModifyFilecontentThreeDoubleDotRelativeUrls()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        // Override Rootpath for this test
        $property = new \ReflectionProperty($this->cachingproxy, 'docrootpath');
        $property->setAccessible(true);
        $property->setValue($this->cachingproxy, '/var/www/');

        $csscontent  = ".bgImageVersion8 {\n";
        $csscontent .= "     background-image: url(\"../../../demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion9 {\n";
        $csscontent .= "     background-image: url(../../../demo/css/css_framework_abc/img/german_flag.jpg);\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion8 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion9 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/var/www/demo/css/css_framework_abc/css_framework_abc.css")));
    }
}