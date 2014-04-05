<?php
/*------------------------------------------------------------------------------

   Project  : CachingProxy
   Filename : tests/AbstractCachingProxyMockClassConstructor.php
   Autor    : (c) Sebastian Krüger <krueger@secra.de>
   Date     : 19.03.2014

   For the full copyright and license information, please view the LICENSE
   file that was distributed with this source code.

   Description: extends the AbstractCachingProxy class only to test
                Constructor habbits, in other cases better use the
                unittest getMockForAbstractClass() function

  ----------------------------------------------------------------------------*/

namespace secra\CachingProxy;

require_once 'overrideRealpathFunction.php';

class AbstractCachingProxyMockClassConstructor extends AbstractCachingProxy
{
    public function getCacheFileExtension()
    {
        // return dummy extension of cached files
        return '.test';
    }

    public function getIncludeHtml()
    {
        return "";
    }

    public function getDocrootpath()
    {
        return $this->docrootpath;
    }

    public function getCachepath()
    {
        return $this->cachepath;
    }

    public function getRelCachepath()
    {
        return $this->relcachepath;
    }
}