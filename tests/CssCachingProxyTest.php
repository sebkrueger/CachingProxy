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


/**
 * CachingProxyTest
 *
 * @category test
 * @package de.secra.cachingproxy
 * @author Sebastian Krüger <krueger@secra.de>
 * @copyright 2014 Sebastian Krüger
 * @license http://www.opensource.org/licenses/MIT The MIT License
 */
class CachingProxyTest extends \PHPUnit_Framework_TestCase {
    private $cachingproxystub;

    public function setUp() {
        // instanciate teststub class for abstract class second param are construtor parmeters
        $this->cachingproxystub = $this->getMockForAbstractClass('\secra\CachingProxy\CachingProxy',array("/null"));
    }

    /**
     * @test
     * @covers secra\Cachingproxy\CachingProxy::addExternFile()
     */
    public function addExternFile() {
        // Add Files with different protocol definations
        $this->cachingproxystub->addFile("https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");
        $this->cachingproxystub->addFile("http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");
        $this->cachingproxystub->addFile("//maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=FALSE");

        // retrun all extern files
        $fileset = $this->cachingproxystub->getIncludeFileset();

        // count the list of files
        $this->assertCount(3, $fileset);
    }
}