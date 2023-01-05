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

use secra\CachingProxy\JsCachingProxy;
/**
 * CachingProxyTest
 *
 * @category test
 * @package de.secra.cachingproxy
 * @author Sebastian Krüger <krueger@secra.de>
 * @copyright 2014 Sebastian Krüger
 * @license http://www.opensource.org/licenses/MIT The MIT License
 */
class JsCachingProxyTest extends \PHPUnit\Framework\TestCase
{
    private $cachingproxy;

    public function setUp() : void
    {
        // instanciate testclass
        $this->cachingproxy = new JsCachingProxy("/","js/cache");
    }

    /**
     * @test
     * @covers secra\Cachingproxy\JsCachingProxy::getCacheFileExtension()
     */
    public function testGetCacheFileExtension()
    {
        // Build reflection of protected function
        $method = new \ReflectionMethod('\secra\CachingProxy\JsCachingProxy', 'getCacheFileExtension');
        $method->setAccessible(true);

        // See if the extension match
        $this->assertEquals(".js", $method->invoke($this->cachingproxy));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\JsCachingProxy::getIncludeHtml()
     */
    public function testGetIncludeHtml()
    {
        // See if we get html file
        $testHtml = $this->cachingproxy->getIncludeHtml();

        // First Test nothing to get out
        $this->assertEquals("", $testHtml);

        // Extern Test URL
        $externUrl = "http://www.example.com/test.js";

        //  Style Tag html
        $scriptTagHtml = '<script type="text/javascript" src="'.$externUrl.'"></script>'."\n";

        // Add the extern File to List
        $this->cachingproxy->addFile($externUrl);

        // get the extended scripttag
        $testHtml = $this->cachingproxy->getIncludeHtml();

        $this->assertStringEndsWith("\n", $testHtml);
        $this->assertEquals($scriptTagHtml, $testHtml);
    }
}