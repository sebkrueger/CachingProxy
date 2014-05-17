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
class CssCachingProxyTest extends \PHPUnit_Framework_TestCase
{
    private $cachingproxy;

    public function setUp()
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
    public function testModifyFilecontent()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod($this->cachingproxy, 'modifyFilecontent');
        $method->setAccessible(true);

        $csscontent  = ".bgImageVersion1 {\n";
        $csscontent .= "     background-image: url(\"./img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion2 {\n";
        $csscontent .= "     background-image: url(./img/german_flag.jpg);\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion3 {\n";
        $csscontent .= "     background-image: url(\"../css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}\n\n";
        $csscontent .= ".bgImageVersion4 {\n";
        $csscontent .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontent .= "}";

        $csscontentexpected  = ".bgImageVersion1 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion2 {\n";
        $csscontentexpected .= "     background-image: url(/demo/css/css_framework_abc/img/german_flag.jpg);\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion3 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}\n\n";
        $csscontentexpected .= ".bgImageVersion4 {\n";
        $csscontentexpected .= "     background-image: url(\"/demo/css/css_framework_abc/img/german_flag.jpg\");\n";
        $csscontentexpected .= "}";

        $this->assertEquals($csscontentexpected, $method->invokeArgs($this->cachingproxy,array($csscontent,"/demo/css/css_framework_abc")));
    }
} 