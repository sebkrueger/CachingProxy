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

use \secra\CachingProxy\JsCachingProxy;
/**
 * CachingProxyTest
 *
 * @category test
 * @package de.secra.cachingproxy
 * @author Sebastian Krüger <krueger@secra.de>
 * @copyright 2014 Sebastian Krüger
 * @license http://www.opensource.org/licenses/MIT The MIT License
 */
class JsCachingProxyTest extends \PHPUnit_Framework_TestCase
{
    private $cachingproxy;

    public function setUp()
    {
        // instanciate testclass
        $this->cachingproxy = new JsCachingProxy();
    }

    /**
     * @test
     * @covers secra\Cachingproxy\JsCachingProxy::getIncludeHtml()
     */
    public function getIncludeHtml()
    {
        // See if we get html file
        $testHtml = $this->cachingproxy->getIncludeHtml();

        // Empty Script Tag html
        $scriptTagHtml = '<script type="text/javascript" src=".js"></script>'."\n";

        $this->assertStringEndsWith("\n", $testHtml);
        $this->assertEquals($scriptTagHtml, $testHtml);
    }
}