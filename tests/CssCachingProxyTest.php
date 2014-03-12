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

// include Testclass
require_once 'CssCachingProxyTestClass.php';

use \secra\CachingProxy\CssCachingProxyTestClass;
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
        $this->cachingproxy = new CssCachingProxyTestClass();
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CssCachingProxy::__construct()
     */
    public function testConstructor()
    {
        // See if we get the right Caching Path
        $this->assertEquals("/demo/css/cache/", $this->cachingproxy->getCachepath());

        // See if the extension match
        $this->assertEquals(".css", $this->cachingproxy->getCachefileExtension());
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
} 